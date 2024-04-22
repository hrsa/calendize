<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSummaryException;
use App\Jobs\GenerateCalendarJob;
use App\Models\IcsEvent;
use App\Models\User;
use App\Resources\IcsEventsResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarGeneratorController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        if (Gate::denies('is-not-blocked')) {
            return response()->json(["error" => "Your account was put on hold!"], Response::HTTP_FORBIDDEN);
        }

        if (Gate::denies('has-credits')) {
            return response()->json(['error' => 'You have no credits left!'], Response::HTTP_FORBIDDEN);
        }

        $icsEvent = IcsEvent::create([
            'user_id' => $request->user()->id,
            'secret' => Str::random(32),
            'prompt' => $request->input('calendarEvent'),
            'timezone' => request('timeZone')
        ]);

        try {
            GenerateCalendarJob::dispatch($icsEvent);
        } catch (\Exception $e) {
            ray($e)->blue();
            return response()->json(["error" => $e->getMessage(), "code" => $e->getCode()]);
        }

        return response()->json(['icsId' => $icsEvent->id]);
    }

    public function guestGenerate(UserService $userService)
    {
        ray('Got a request', request()->all())->orange();

        if (User::where('email', request()->email)->exists()) {
            return response()->json(['error' => 'You already have an account!'], status: Response::HTTP_UNAUTHORIZED);
        }

        $user = $userService->createWithCredits(email: request('email'));

        $icsEvent = IcsEvent::create([
            'user_id' => $user->id,
            'secret' => Str::random(32),
            'prompt' => request('calendarEvent'),
            'timezone' => request('timeZone')
        ]);

        GenerateCalendarJob::dispatch($icsEvent);

        return response()->json(['reply' => 'ok']);
    }

    /**
     * @throws NoSummaryException
     */
    public function downloadEvent(int $id, string $secret): StreamedResponse
    {
        $icsEvent = IcsEvent::findOrFail($id);

        abort_if($icsEvent->secret !== $secret, Response::HTTP_FORBIDDEN, message: "The secret code is wrong!");

        return response()->streamDownload(static function () use ($icsEvent) {
            echo $icsEvent->ics;
        }, $icsEvent->getSummary() . '.ics', ['Content-Type' => 'text/calendar']);
    }

    public function usersEvents(): \Inertia\Response
    {
        $icsEvents = IcsEvent::whereUserId(request()->user()->id)
            ->whereNotNull('ics')
            ->orderByDesc('id')
            ->paginate(10);

        return Inertia::render('MyEvents', ['events' => IcsEventsResource::collection($icsEvents)]);
    }
}
