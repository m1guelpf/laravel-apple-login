<?php

namespace M1guelpf\LoginWithApple;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use M1guelpf\LoginWithApple\Commands\GenerateClientSecret;
use M1guelpf\LoginWithApple\Socialite\SignInWithAppleProvider;

class LoginWithAppleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (LoginWithApple::$registersRoutes) {
            $middleware = array_filter($this->app['router']->getMiddlewareGroups()['web'], fn ($middleware) => $middleware != 'App\Http\Middleware\VerifyCsrfToken');

            Route::middleware($middleware)->group(function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });
        }

        if (LoginWithApple::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([GenerateClientSecret::class]);

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'apple-migrations');
        }

        Socialite::extend('apple', fn () => Socialite::buildProvider(SignInWithAppleProvider::class, array_merge(['redirect' => '/login/apple'], config('services.apple'))));
    }

    public function register()
    {
        //
    }
}
