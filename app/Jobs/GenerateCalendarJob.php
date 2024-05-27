<?php

namespace App\Jobs;

use App\Data\IcsEvent\IcsEventData;
use App\Data\IcsEvent\IcsEventsArray;
use App\Events\IcsEventProcessed;
use App\Helpers\IcsGenerator;
use App\Mail\IcsError;
use App\Mail\IcsValid;
use App\Models\IcsEvent;
use App\Models\User;
use App\Notifications\Telegram\User\IcsEventError;
use App\Notifications\Telegram\User\IcsEventValid;
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

    private array $aiMessages;

    public function __construct(public icsEvent $icsEvent)
    {
        $this->aiMessages = [];
    }

    public function handle(): void
    {
        $user = $this->icsEvent->user;
        $result = null;
        $systemPrompt = config('openai.system_prompt');
        $now = Carbon::now();
        $systemPrompt .= " As of today, the date is {$now}. If not present in my data, add {$user->name} {$user->email} as attendee.";

        if ($this->icsEvent->timezone) {
            $systemPrompt .= "User's timezone: {$this->icsEvent->timezone}.";
        }

        $this->aiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $this->icsEvent->prompt],
        ];

        try {
            $result = $this->generateOpenAIResponse();
        } catch (Exception $e) {
            Log::alert("OpenAI error generating IcsEvent #{$this->icsEvent->id}: {$e->getMessage()}");
        }

        if (!$result) {
            try {
                $result = $this->generateMistralResponse();
            } catch (Exception $e) {
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
                Log::alert("Mistral error generating IcsEvent #{$this->icsEvent->id}: {$e->getMessage()}");
                IcsEventProcessed::dispatch($this->icsEvent);
                $this->fail("{$e->getCode()} : {$e->getMessage()}");

                return;
            }
        }

        $jsonIcs = $result->choices[0]->message->content;

        if (!$jsonIcs) {
            $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
            Log::alert("Total failure for IcsEvent #{$this->icsEvent->id}");
            IcsEventProcessed::dispatch($this->icsEvent);
            $this->fail("Total failure for IcsEvent #{$this->icsEvent->id}");

            return;
        }

        $this->aiMessages[] = ['role' => 'assistant', 'content' => $jsonIcs];
        $tokenUsage = $result->usage?->totalTokens ?? $result->usage->total_tokens;
        $this->icsEvent->update(['token_usage' => $tokenUsage]);

        $decodedReply = json_decode($jsonIcs, true);

        if (array_key_exists('error', $decodedReply) && isset($decodedReply['error'])) {
            $this->icsEvent->update(['error' => $decodedReply['error']]);
            $user->increment('failed_requests');

            if ($user->failed_requests >= $user->credits) {
                $user->decrement('credits');
            }
            $this->notifyUserError($user);
            IcsEventProcessed::dispatch($this->icsEvent);

            return;
        }

        if (array_key_exists('events', $decodedReply) && isset($decodedReply['events'])) {
            try {
                $events = IcsEventsArray::from($decodedReply);
                $this->generateAndSaveIcs(event: null, events: $events, user: $user);
            } catch (Exception $e) {
                Log::error("Ics {$this->icsEvent->id} error: {$e->getMessage()}", $decodedReply);
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
                Log::alert("Total failure for IcsEvent #{$this->icsEvent->id}");
                IcsEventProcessed::dispatch($this->icsEvent);
                $this->fail("Total failure for IcsEvent #{$this->icsEvent->id}");

                return;
            }
        } else {
            try {
                $event = IcsEventData::from($decodedReply);
                $this->generateAndSaveIcs(event: $event, events: null, user: $user);
            } catch (Exception $e) {
                Log::error("Ics {$this->icsEvent->id} error: {$e->getMessage()}", $decodedReply);
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
                Log::alert("Total failure for IcsEvent #{$this->icsEvent->id}");
                IcsEventProcessed::dispatch($this->icsEvent);
                $this->fail("Total failure for IcsEvent #{$this->icsEvent->id}");

                return;
            }
        }
    }

    private function generateOpenAIResponse(): CreateResponse
    {
        return OpenAI::chat()->create([
            'model'           => 'gpt-4-turbo',
            'messages'        => $this->aiMessages,
            'max_tokens'      => 3700,
            'response_format' => ['type' => 'json_object'],
        ]);
    }

    /**
     * @throws Exception
     */
    private function generateMistralResponse(): stdClass
    {
        $response = Http::mistral()->timeout(10)->post('/chat/completions', [
            'model'           => 'mistral-large-latest',
            'messages'        => $this->aiMessages,
            'max_tokens'      => 3700,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->failed()) {
            throw new Exception('Mistral HTTP Error: ' . $response->body());
        }

        $result = $response->json();

        return json_decode(json_encode($result));
    }

    private function notifyUserSuccess(User $user): void
    {
        Mail::to($user->email)->send(new IcsValid($this->icsEvent));

        if ($user->telegram_id && $user->send_tg_notifications) {
            $user->notify(new IcsEventValid(icsEvent: $this->icsEvent, message: 'Your event was calendized! 🤗'));
        }
    }

    private function notifyUserError(User $user): void
    {
        Mail::to($user->email)->send(new IcsError($this->icsEvent));

        if ($user->telegram_id && $user->send_tg_notifications) {
            $user->notify(new IcsEventError($this->icsEvent));
        }
    }

    /**
     * @throws Exception
     */
    private function generateAndSaveIcs(?IcsEventData $event, ?IcsEventsArray $events, User $user): void
    {
        $calendar = IcsGenerator::generateCalendar(event: $event, events: $events);
        $this->icsEvent->update(['ics' => $calendar->get()]);
        $user->decrement('credits');

        $this->notifyUserSuccess($user);
        IcsEventProcessed::dispatch($this->icsEvent);
    }
}
