<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function github(): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::query()->updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name ?? $githubUser->email,
            'email' => $githubUser->email,
            'password' => bcrypt(str()->random(20)),
//            'github_token' => $githubUser->token,
//            'github_refresh_token' => $githubUser->refreshToken,
        ]);

        auth()->login($user);

        return redirect()
            ->intended(route('home'));

    }
}
