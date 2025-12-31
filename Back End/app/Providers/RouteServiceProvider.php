<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

// It is a service provider that tells Laravel where your routes are located and how to load them.
// Laravel loads:
// web routes
// api routes
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     * This method runs when your application starts.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        // Loads all routes in routes/api.php
        // With:
        // middleware: api
        // URL prefix: /api
        // → So Route::get('/users') becomes /api/users
        // Loads all routes in routes/web.php
        // With:
        // middleware: web
        // This includes sessions, cookies, CSRF protection.
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     * This part controls how many requests a user can make per minute:
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });
    }
}
