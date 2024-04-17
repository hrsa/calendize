<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    public function createWithCredits(string $email, string|null $password = null, int|null $credits = 1): User
    {
        return User::create([
            'email' => $email,
            'password' => bcrypt($password ?? Str::random(10)),
            'credits' => $credits
        ]);
    }
}
