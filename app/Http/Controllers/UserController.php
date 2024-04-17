<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function signup(UserService $userService): JsonResponse
    {
        if (User::where('email', request('email'))->exists()) {
            return response()->json(['error' => 'Email already exists'], Response::HTTP_FORBIDDEN);
        }

        $user = $userService->createWithCredits(email: request('email'), credits: 10);

        Auth::login($user);

        return response()->json(['token' => $user->createToken('signup')]);
    }

    public function login(): JsonResponse
    {
        $user = User::where('email', request('email'))->first();

        if ($user && !$user->blocked) {
            Auth::login($user);
            return response()->json(['token' => $user->createToken('signup')]);
        } else {
            return response()->json(['error' => 'You are BANNED'], Response::HTTP_FORBIDDEN);
        }
    }

    public function checkEmail(): JsonResponse
    {
        if (User::where('email', request('email'))->exists()) {
            return response()->json(['error' => 'This email already exists'], Response::HTTP_FORBIDDEN);
        }

        return response()->json('ok');
    }
}
