<?php

namespace M1guelpf\LoginWithApple;

class LoginWithApple
{
    /**
     * Indicates if this package's migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if this package's routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * The callback that is responsible for redirecting the user to the login.
     *
     * @var callable|null
     */
    public static $redirectCallback;

    /**
     * The callback that is responsible for retrieving the user authenticated by Apple.
     *
     * @var callable|null
     */
    public static $retrieveUserCallback;

    /**
     * Register a callback that is responsible for redirecting the user to the login.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function redirectUsing(callable $callback)
    {
        static::$redirectCallback = $callback;
    }

    /**
     * Register a callback that is responsible for retrieving the user authenticated by Apple.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function retrieveUserWith(callable $callback)
    {
        static::$retrieveUserCallback = $callback;
    }

    /**
     * Configure this package to not register its migrations.
     *
     * @return void
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;
    }

    /**
     * Configure this package to not register its routes.
     *
     * @return void
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;
    }
}
