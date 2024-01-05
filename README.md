# Laravel JWT Authorization

This repository provides full functionality for authorization and mail confirmation using JWT (JSON Web Tokens) in a Laravel application.

## Installation

To set up JWT authentication and mail confirmation, follow these steps:

1. Run the following command to install the `skofi/laravel-jwt-auth` package:
    ```sh
    composer require skofi/laravel-jwt-auth
    ```

2. Run the following command to install JWT authentication:
    ```sh
    php artisan jwt-auth:install
    ```

3. Publish the vendor for JWT authentication:
    ```sh
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    ```

## Middleware for JWT

Add the following middleware for JWT authentication in your `Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
];
```

## User Model Implementation

Implement the JWTSubject interface in the User model as follows:

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // ... (other user model code)

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

## Password Reset Route Configuration

In the AuthServiceProvider of the boot() method, add the following code to configure the password reset route to redirect to the frontend URL:

```php
use Illuminate\Auth\Notifications\ResetPassword;

// ... (other imports and code)

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
    */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . $user->email;
        });
    }
    // ...
}
```

## Time-to-live JWT

You can define variables in your `.env` file to customize the token's time-to-live (TTL) settings:

```
JWT_TTL=1440
JWT_REMEMBER_TTL=525600
```
- `JWT_TTL` denotes the standard token lifetime in minutes.
- `JWT_REMEMBER_TTL` represents the token's lifetime when the 'Remember me' option is selected, also in minutes.

## Generate secret key

This key serves as the signature for your tokens, and the process will depend on the algorithm you choose to use.

```php
php artisan jwt:secret
```

## Usage
- Once the setup is complete, you can use JWT authentication for user authorization.
- Password reset links will be generated based on the configured `FRONTEND_URL=` in your `.env`.

## Contribution
Feel free to contribute by opening issues or submitting pull requests.

## License
This project is licensed under the [MIT](https://github.com/SkofiTI/laravel-jwt-auth/blob/main/LICENSE) License.
