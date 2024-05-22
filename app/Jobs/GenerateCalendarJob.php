<?php

namespace App\Jobs;

use App\Events\IcsEventProcessed;
use App\Mail\IcsError;
use App\Mail\IcsValid;
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
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use stdClass;

class GenerateCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $tries = 5;

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
            Log::alert("OpenAI error generating IcsEvent #{$this->icsEvent->id}: {$e->getMessage()}");
        }

        if (!$result) {
            try {
                $result = $this->generateMistralResponse($systemPrompt);
            } catch (Exception $e) {
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
                Log::alert("Mistral error generating IcsEvent #{$this->icsEvent->id}: {$e->getMessage()}");
                IcsEventProcessed::dispatch($this->icsEvent);
                $this->fail("{$e->getCode()} : {$e->getMessage()}");

                return;
            }
        }

        $jsonIcs = $result?->choices[0]->message->content;

        if (!$jsonIcs) {
            $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
            Log::alert("Total failure for IcsEvent #{$this->icsEvent->id}");
            IcsEventProcessed::dispatch($this->icsEvent);
            $this->fail("Total failure for IcsEvent #{$this->icsEvent->id}");
        }

        $decodedReply = json_decode($jsonIcs, true);
        if (array_key_exists('ics', $decodedReply) && isset($decodedReply['ics'])) {
            $this->icsEvent->update(['ics' => $decodedReply['ics']]);
            $this->icsEvent->user->decrement('credits');
            Mail::to($this->icsEvent->user->email)->send(new IcsValid($this->icsEvent));
        } else {
            $this->icsEvent->update(['error' => $decodedReply['error']]);
            $this->icsEvent->user->increment('failed_requests');
            Mail::to($this->icsEvent->user->email)->send(new IcsError($this->icsEvent));
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
            'model'    => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $this->icsEvent->prompt],
            ],
            'max_tokens'      => 2800,
            'response_format' => ['type' => 'json_object'],
        ]);
    }

    /**
     * @throws Exception
     */
    private function generateMistralResponse(string $systemPrompt): stdClass
    {
        $response = Http::mistral()->timeout(10)->post('/chat/completions', [
            'model'    => 'mistral-large-latest',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $this->icsEvent->prompt],
            ],
            'max_tokens'      => 2800,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->failed()) {
            throw new Exception('Mistral HTTP Error: ' . $response->body(), $response->getStatusCode());
        }

        $result = $response->json();

        return json_decode(json_encode($result));
    }
}
