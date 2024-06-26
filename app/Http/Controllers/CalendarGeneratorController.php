<?php

namespace App\Http\Controllers;

use App\Exceptions\NoSummaryException;
use App\Http\Requests\IcsEventRequest;
use App\Models\IcsEvent;
use App\Models\User;
use App\Resources\IcsEventsResource;
use App\Services\IcsEventService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarGeneratorController extends Controller
{
    public function generate(IcsEventRequest $request, IcsEventService $icsEventService): JsonResponse
    {
        if (Gate::denies('errors-under-threshold')) {
            return response()->json(['error' => 'You have generated more errors than credits - this is suspected as spam. Please top-up your account to reset the error count.'], Response::HTTP_FORBIDDEN);
        }

        if (Gate::denies('has-credits')) {
            return response()->json(['error' => 'You have no credits left!'], Response::HTTP_FORBIDDEN);
        }

        $icsEvent = $icsEventService->createIcsEvent(userId: $request->user()->id, prompt: $request->calendarEvent, timezone: $request->timeZone, dispatchJob: true);

        return response()->json(['icsId' => $icsEvent->id]);
    }

    public function guestGenerate(IcsEventRequest $request, UserService $userService, IcsEventService $icsEventService)
    {
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'You need to verify your account to use Calendize!'], status: Response::HTTP_UNAUTHORIZED);
        }

        $user = $userService->createGuestWithCredits(email: $request->email);

        Auth::login($user);

        $icsEventService->createIcsEvent(userId: $user->id, prompt: $request->calendarEvent, timezone: $request->timeZone);

        $user->sendEmailVerificationNotification();

        return response()->noContent();
    }

    /**
     * @throws NoSummaryException
     */
    public function downloadEvent(int $id, string $secret): StreamedResponse
    {
        $icsEvent = IcsEvent::findOrFail($id);

        abort_if($icsEvent->secret !== $secret, Response::HTTP_FORBIDDEN, message: 'The secret code is wrong!');

        return response()->streamDownload(static function () use ($icsEvent) {
            echo $icsEvent->ics;
        }, $icsEvent->getSummary() . '.ics', ['Content-Type' => 'text/calendar']);
    }

    public function usersEvents(): \Inertia\Response
    {
        $icsEvents = Auth::user()
            ->processedIcsEvents()
            ->latest()
            ->paginate(10);

        return Inertia::render('MyEvents', ['events' => IcsEventsResource::collection($icsEvents)]);
    }
}
