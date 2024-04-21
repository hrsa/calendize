<?php

namespace App\Jobs;

use App\Events\IcsEventProcessed;
use App\Models\IcsEvent;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use stdClass;

class GenerateCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public icsEvent $icsEvent)
    {
    }

    public function handle(): void
    {
        $result = null;
        $systemPrompt = config('openai.system_prompt');
        $now = Carbon::now();
        $systemPrompt .= " As of today, the date is {$now}.";

        if ($this->icsEvent->timezone) {
            $systemPrompt .= "User's timezone: " . $this->icsEvent->timezone;
        }


        try {
            $result = $this->generateOpenAIResponse($systemPrompt);
        } catch (Exception $e) {
            Log::alert("OpenAI error generating IcsEvent #{$this->icsEvent->id}");
        }

        if (!$result) {
            try {
                $result = $this->generateMistralResponse($systemPrompt);
            } catch (Exception $e) {
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
                IcsEventProcessed::dispatch($this->icsEvent);
                $this->fail($e);
            }
        }

        $jsonIcs = $result?->choices[0]->message->content;

        if (!$jsonIcs) {
            $this->release(300);
        }

        $decodedReply = json_decode($jsonIcs, true);
        if (array_key_exists('ics', $decodedReply) && isset($decodedReply['ics'])) {
            $this->icsEvent->update(['ics' => $decodedReply['ics']]);
            $this->icsEvent->user->decrement('credits');
        } else {
            $this->icsEvent->update(['error' => $decodedReply['error']]);
            $this->icsEvent->user->increment('failed_requests');

            if ($this->icsEvent->user->failed_requests >= $this->icsEvent->user->credits) {
                $this->icsEvent->user->decrement('credits');
            }
        }


        $tokenUsage = $result->usage?->totalTokens ?? $result->usage->total_tokens;
        $this->icsEvent->update(['token_usage' => $tokenUsage]);
        IcsEventProcessed::dispatch($this->icsEvent);
    }

    private function generateOpenAIResponse(string $systemPrompt): CreateResponse
    {
        return OpenAI::chat()->create([
            'model' => 'gpt-4-turbo-2024-04-09',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $this->icsEvent->prompt],
            ],
            'max_tokens' => 2800,
            'response_format' => ['type' => 'json_object']
        ]);
    }

    private function generateMistralResponse(string $systemPrompt): stdClass
    {
        $result = Http::mistral()->post("/chat/completions", [
            "model" => "mistral-large-latest",
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $this->icsEvent->prompt],
            ],
            'max_tokens' => 2800,
            'response_format' => ['type' => 'json_object']
        ])->json();

        return json_decode(json_encode($result));
    }
}
