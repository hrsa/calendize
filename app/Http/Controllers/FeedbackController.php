<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use App\Models\IcsEvent;
use App\Notifications\Telegram\Admin\FeedbackReceived;
use Exception;
use Illuminate\Support\Facades\Notification;

class FeedbackController extends Controller
{
    public function store(FeedbackRequest $request)
    {
        $ics_event = IcsEvent::whereId($request->ics_event_id)
            ->whereUserId($request->user()->id)
            ->first();

        if (!$ics_event) {
            return response()->json(['error' => 'Unauthorized to provide feedback for this event.'], 403);
        }

        try {
            $feedback = Feedback::create($request->validated());

            Notification::route('telegram', config('app.admin.telegram_chat_id'))
                ->notify(new FeedbackReceived($feedback));

            return response()->json(['message' => 'Feedback saved.'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to save feedback.'], 500);
        }
    }
}
