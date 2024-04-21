<?php

namespace App\Jobs;

use App\Events\IcsEventProcessed;
use App\Models\IcsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateCalendarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(public icsEvent $icsEvent)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $systemPrompt = config('openai.system_prompt');

        $now = Carbon::now();
        $systemPrompt .= " As of today, the date is {$now}.";

        if ($this->icsEvent->timezone) {
            $systemPrompt .= "User's timezone: " . $this->icsEvent->timezone;
        }

        try {
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo-2024-04-09',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $this->icsEvent->prompt],
                ],
                'max_tokens' => 2800,
                'response_format' => ['type' => 'json_object']
            ]);
        } catch (\Exception $e) {
            $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
            IcsEventProcessed::dispatch($this->icsEvent);
            $this->fail($e);
        }

        $reply = $result?->choices[0]->message->content;

        if (!$reply) {
            $this->release(300);
        }

        $decodedReply = json_decode($reply, true);

        if (array_key_exists('error', $decodedReply)) {
            $this->icsEvent->update(['error' => $decodedReply['error']]);
            $this->icsEvent->user->increment('failed_requests');

            if ($this->icsEvent->user->failed_requests >= $this->icsEvent->user->credits) {
                $this->icsEvent->user->decrement('credits');
            }
        } else {
            $this->icsEvent->update(['ics' => $decodedReply['ics']]);
            $this->icsEvent->user->decrement('credits');
        }

        ray('Got a reply', $reply)->green();
        ray($result->usage->totalTokens)->blue();
        $this->icsEvent->update('token_usage', $result->usage->totalTokens);
        IcsEventProcessed::dispatch($this->icsEvent);
        ray('broadcasting')->orange();
    }
}
