<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCalendar;
use App\Models\IcsEvent;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CalendarGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        ray("Got a auth request", $request->all())->red();
        ray($request->session());

        $icsEvent = IcsEvent::create([
            'user_id' => $request->user()->id,
            'prompt' => $request->input('calendarEvent'),
            'timezone' => request('timeZone')
        ]);

        GenerateCalendar::dispatch($icsEvent);

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
            'prompt' => request('calendarEvent'),
            'timezone' => request('timeZone')
        ]);

        GenerateCalendar::dispatch($icsEvent);

        return response()->json(['reply' => 'ok']);
    }
}
