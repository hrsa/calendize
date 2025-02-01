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

    private array $aiMessages = [];

    public function __construct(public icsEvent $icsEvent) {}

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $user = $this->icsEvent->user;
        $result = null;
        $systemPrompt = config('openai.system_prompt');
        $now = Carbon::now();
        $systemPrompt .= " As of today, the date is {$now}. Pay attention to the year - if there's no year in the data, but the event seems to be in the past - then it's next year! If not present in my data, add {$user->name} {$user->email} as attendee and organizer.";

        if ($this->icsEvent->timezone) {
            $systemPrompt .= "User's timezone: {$this->icsEvent->timezone}.";
        } elseif ($user->timezone) {
            $systemPrompt .= "User's timezone: {$user->timezone}.";
        }

        $this->aiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $this->icsEvent->prompt],
        ];

        $maxRetries = 4;
        $attempts = 0;

        while ($attempts < $maxRetries) {
            try {
                $result = $this->generateOpenAIResponse();

                if ($this->isErrorJson($result)) {
                    $attempts++;
                    Log::warning("OpenAI Retry #{$attempts} for IcsEvent #{$this->icsEvent->id}. Reason: {$result->choices[0]->message->content}");

                    continue;
                }

                break;
            } catch (Exception $exception) {
                Log::alert("OpenAI error generating IcsEvent #{$this->icsEvent->id}: {$exception->getMessage()}");
                break;
            }
        }

        if (!isset($result)) {
            $attempts = 0;

            while ($attempts < $maxRetries) {
                try {
                    $result = $this->generateMistralResponse();

                    if ($this->isErrorJson($result)) {
                        $attempts++;
                        Log::warning("Mistral Retry #{$attempts} for IcsEvent #{$this->icsEvent->id}. Reason: " . json_encode($result));

                        continue;
                    }

                    break;
                } catch (Exception $e) {
                    Log::alert("Mistral error generating IcsEvent #{$this->icsEvent->id}: {$e->getMessage()}");
                    break;
                }
            }
        }

        if (!isset($result) || $this->isErrorJson($result)) {
            if (isset($result) && $this->isErrorJson($result)) {
                $errorContent = json_decode($result->choices[0]->message->content, true)['error'] ?? 'Unexpected error';
                $this->icsEvent->update(['error' => $errorContent]);
            } else {
                $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
            }

            Log::alert("Max retries reached for IcsEvent #{$this->icsEvent->id}");
            IcsEventProcessed::dispatch($this->icsEvent);
            $this->notifyUserError($this->icsEvent->user);
            $this->fail("Max retries reached for IcsEvent #{$this->icsEvent->id}");

            return;
        }

        $this->processResponse($result, $user);
    }

    private function isErrorJson(mixed $result): bool
    {
        if (!$result) {
            return false;
        }

        $content = $result->choices[0]->message->content ?? null;

        if ($content) {
            $decoded = json_decode($content, true);

            return array_key_exists('error', $decoded) && isset($decoded['error']);
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function processResponse(mixed $result, User $user): void
    {
        $jsonIcs = $result->choices[0]->message->content;

        if (!$jsonIcs) {
            $this->icsEvent->update(['error' => "I'm sorry, my servers are having hiccups. Please try again in 30-60 minutes!"]);
            Log::alert("Total failure for IcsEvent #{$this->icsEvent->id}");
            IcsEventProcessed::dispatch($this->icsEvent);
            $this->notifyUserError($this->icsEvent->user);
            $this->fail("Total failure for IcsEvent #{$this->icsEvent->id}");

            return;
        }

        $this->aiMessages[] = ['role' => 'assistant', 'content' => $jsonIcs];
        $tokenUsage = $result->usage?->totalTokens ?? $result->usage->total_tokens;
        $this->icsEvent->update(['token_usage' => $tokenUsage]);

        $decodedReply = json_decode($jsonIcs, true);

        if (isset($decodedReply['events'])) {
            $this->handleEvents($decodedReply, $user);
        } else {
            $this->handleEvent($decodedReply, $user);
        }
    }

    /**
     * Handle the case where multiple events are returned
     *
     * @throws Exception
     */
    private function handleEvents(array $decodedReply, User $user): void
    {
        $events = IcsEventsArray::from($decodedReply);
        $this->generateAndSaveIcs(event: null, events: $events, user: $user);
    }

    /**
     * Handle the case where a single event is returned
     *
     * @throws Exception
     */
    private function handleEvent(array $decodedReply, User $user): void
    {
        $event = IcsEventData::from($decodedReply);
        $this->generateAndSaveIcs(event: $event, events: null, user: $user);
    }

    private function generateOpenAIResponse(): CreateResponse
    {
        return OpenAI::chat()->create([
            'model'           => 'gpt-4o',
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
