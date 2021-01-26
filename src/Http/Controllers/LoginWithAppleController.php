<?php

namespace M1guelpf\LoginWithApple\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Laravel\Socialite\Facades\Socialite;
use M1guelpf\LoginWithApple\LoginWithApple;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginWithAppleController
{
    public function redirect()
    {
        return with(Socialite::driver('apple')->redirect(), function (RedirectResponse $redirectResponse) {
            return LoginWithApple::$redirectCallback ? call_user_func(LoginWithApple::$redirectCallback, $redirectResponse->getTargetUrl()) : $redirectResponse;
        });
    }

    public function login()
    {
        $appleUser = Socialite::driver('apple')->user();

        $user = LoginWithApple::$retrieveUserCallback ? call_user_func(LoginWithApple::$retrieveUserCallback, $appleUser) : $this->getUserModel()->where('apple_id', $appleUser->getId())->first();

        if (! $user) {
            return redirect()->route('login');
        }

        $user->update(['apple_id' => $appleUser->getId()]);

        Auth::login($user);

        // Due to how Sign in with Apple is implemented, we can't return a server redirect if we want to
        // persist the session we just created. Instead, we will return an HTML template that
        // instantly redirects the user, persisting our newly-authenticated user.
        return $this->frontendRedirectTo(\App\Providers\RouteServiceProvider::HOME);
    }

    protected function getUserModel() : Model
    {
        return app(config('auth.providers.users.model'));
    }

    protected function frontendRedirectTo(string $url) : string
    {
        return sprintf('<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="refresh" content="0;url=\'%1$s\'" />

                <title>Redirecting to %1$s</title>
            </head>
            <body style="display: none;">
                Redirecting to <a href="%1$s">%1$s</a>.
                <script>setTimeout(() => document.body.style.display = null, 1000)</script>
            </body>
        </html>', htmlspecialchars($url, \ENT_QUOTES, 'UTF-8'));
    }
}
