<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'petugas' => \App\Http\Middleware\PetugasMiddleware::class,
        'user' => \App\Http\Middleware\UserMiddleware::class,
    ];

    /**
     * The application's middleware aliases (Laravel 13+).
     *
     * @var array
     */
    protected $middlewareAliases = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'petugas' => \App\Http\Middleware\PetugasMiddleware::class,
        'user' => \App\Http\Middleware\UserMiddleware::class,
    ];

    /**
     * Sync middleware groups and aliases to the router (needed for newer Kernel behavior).
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        if (! isset($this->router)) {
            return;
        }

        foreach ($this->middlewareGroups as $key => $group) {
            $this->router->middlewareGroup($key, $group);
        }

        $aliases = $this->middlewareAliases ?? $this->routeMiddleware ?? [];

        foreach ($aliases as $key => $class) {
            $this->router->aliasMiddleware($key, $class);
        }
    }
}
