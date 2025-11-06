<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;


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
    Event::listen(Login::class, function ($event) {
        activity()
            ->causedBy($event->user)
            ->event('login')
            ->log('User login');
    });

    Event::listen(Logout::class, function ($event) {
        activity()
            ->causedBy($event->user)
            ->event('logout')
            ->log('User logout');
    });

    DB::statement("SET time_zone = '+07:00'");

}
}
