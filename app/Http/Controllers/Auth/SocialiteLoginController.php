<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth1\Client\Credentials\TemporaryCredentials;

class SocialiteLoginController extends Controller
{
    public function redirectToGoogle()
    {
        $url = Socialite::driver('google')->redirect();

        return Inertia::location($url);
    }

    public function handleGoogleCallback(UserService $userService)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = $userService->createOrGetSocialiteUser(
            email: $googleUser->getEmail(),
            name: $googleUser->getName(),
            provider: 'google',
            provider_id: $googleUser->getId());

        Auth::login($user);

        return redirect()->route('how-to-use');
    }

    public function redirectToLinkedin()
    {
        $url = Socialite::driver('linkedin-openid')->redirect();

        return Inertia::location($url);
    }

    public function handleLinkedinCallback(UserService $userService)
    {
        $linkedinUser = Socialite::driver('linkedin-openid')->stateless()->user();
        $user = $userService->createOrGetSocialiteUser(
            email: $linkedinUser->getEmail(),
            name: $linkedinUser->getName(),
            provider: 'linkedin',
            provider_id: $linkedinUser->getId());

        Auth::login($user);

        return redirect()->route('how-to-use');
    }

    public function redirectToTwitter()
    {
        $url = Socialite::driver('twitter')->redirect();

        return Inertia::location($url);
    }

    public function handleTwitterCallback(UserService $userService)
    {
        $credentials = new TemporaryCredentials();
        $credentials->setIdentifier(request()->oauth_token);
        $credentials->setSecret(request()->oauth_verifier);
        request()->session()->put('oauth', ['temp' => $credentials]);

        $twitterUser = Socialite::driver('twitter')->user();
        $user = $userService->createOrGetSocialiteUser(
            email: $twitterUser->getEmail(),
            name: $twitterUser->getName(),
            provider: 'twitter',
            provider_id: $twitterUser->getId());

        Auth::login($user);

        return redirect()->route('how-to-use');
    }
}
