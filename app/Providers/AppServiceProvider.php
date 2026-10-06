<?php

namespace App\Providers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'layouts.admin',
            'layouts.petugas',
            'layouts.user',
        ], function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();

                $notifications = Notifikasi::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadNotifications = Notifikasi::where('user_id', $userId)
                    ->where('is_read', false)
                    ->count();
            } else {
                $notifications = collect();
                $unreadNotifications = 0;
            }

            $view->with(compact('notifications', 'unreadNotifications'));
        });
    }
}
