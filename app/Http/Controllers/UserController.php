<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class UserController extends Controller
{
    public function checkEmail(): JsonResponse|Response
    {
        if (User::where('email', request('email'))->exists()) {
            return response()->json(['error' => 'This email already exists'], SymfonyResponse::HTTP_FORBIDDEN);
        }

        return response()->noContent();
    }

    public function hidePasswordReminder(): Response
    {
        request()->user()->update(['hide_pw_reminder' => today()]);

        return response()->noContent();
    }
}
