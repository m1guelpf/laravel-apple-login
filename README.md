# Plug-and-play Sign in with Apple for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/m1guelpf/laravel-apple-login.svg?style=flat-square)](https://packagist.org/packages/m1guelpf/laravel-apple-login)
[![Total Downloads](https://img.shields.io/packagist/dt/m1guelpf/laravel-apple-login.svg?style=flat-square)](https://packagist.org/packages/m1guelpf/laravel-apple-login)

## Installation

You can install the package via composer:

```bash
composer require m1guelpf/laravel-apple-login
```

You'll first need to create an [App ID](https://developer.apple.com/account/resources/identifiers/bundleId/add/bundle) for your website, by setting an explicit Bundle ID and enabling Sign In With Apple.

You'll then need to create a [Service ID](https://developer.apple.com/account/resources/identifiers/serviceId/add/) for your web app, and checking and configuring Sign in with Apple using the App ID created earlier and `/apple/login` as your return URL. The identifier from your Service ID will be your client id.

Finally, you'll need to create a [Private Key](https://developer.apple.com/account/resources/authkeys/add) for Sign in with Apple and your App ID. Once downloaded, use the `php artisan apple:secret path/to/key.p8` to generate a client secret.

Once you have your client id and secret, you should add them to the `config/services.php` file as follows:

```php
'apple' => [
  'client_id' => env('APPLE_ID'),
  'client_secret' => env('APPLE_SECRET'),
],
```

## Usage

To initiate the login procedure, just add a link to `/login/apple` somewhere on your login page. By default, this package will only login the user if they already had an account with the same email, but this can be customized by providing a `retrieveUser` callback on your `AuthServiceProvider`, where you can create a new user if needed:

```php
use M1guelpf\LoginWithApple\LoginWithApple;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ...

        LoginWithApple::retrieveUserWith(function (\Laravel\Socialite\Two\User $appleUser) {
            if ($user = User::where('apple_id', $appleUser->getId())->first() || ! $appleUser->getEmail()) {
                return $user;
            }

            return User::create(['name' => $appleUser->getName(), 'email' => $appleUser->getEmail()]);
        });
    }
}
```

> Note: Due to Apple's implementation, you'll only have access to the user's name and email on their first login, and will only be able to reference them by their ID afterwards. You should structure your user retrieval code to take this limitation into account.

### Usage with Inertia.js

By default, the `/login/apple` route returns a `RedirectResponse`. If you want to customize this behaviour (to return an Inertia redirect, for example), you can use the `redirectUsing` method on your `AuthServiceProvider`.

```php
use Inertia\Inertia;
use M1guelpf\LoginWithApple\LoginWithApple;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ...

        LoginWithApple::redirectUsing(fn (string $url) => Inertia::location($url));
    }
}
```

### Manually configuring routing or migrations

By default, this package will add a nullable `apple_id` column to your users table and register its own routes (GET and POST `/login/apple`). If you wish to opt out of any of these, you can do so by calling `ignoreMigrations` or `ignoreRoutes` on your `AuthServiceProvider`.

```php
use M1guelpf\LoginWithApple\LoginWithApple;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ...

        LoginWithApple::ignoreMigrations();
        LoginWithApple::ignoreRoutes();
    }
}
```

You can publish the migrations to your app by running `php artisan vendor:publish --tag=apple-migrations`. You can also register your own routes pointing to this package's `LoginWithAppleController` (GET `redirect()` and POST `login()`) to avoid re-implementing the backend logic.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Miguel Piedrafita](https://github.com/m1guelpf)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
