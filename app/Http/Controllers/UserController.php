<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function checkEmail(): JsonResponse
    {
        if (User::where('email', request('email'))->exists()) {
            return response()->json(['error' => 'This email already exists'], Response::HTTP_FORBIDDEN);
        }

        return response()->json('ok');
    }
}
