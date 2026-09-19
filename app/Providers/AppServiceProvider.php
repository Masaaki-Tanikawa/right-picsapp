<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageManager::class, fn () => new ImageManager(new Driver));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $user = Auth::user();

            $view->with([
                'unreadNotificationCount' => $user ? $user->unreadNotifications()->count() : 0,
                'recentNotifications' => $user ? $user->notifications()->latest()->limit(10)->get() : collect(),
            ]);
        });
    }
}
