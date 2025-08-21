<?php

namespace App\Providers;

use App\Models\Agreement;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
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
        Event::listen(
            \Namu\WireChat\Events\MessageCreated::class,
            SendEmailVerificationNotification::class,
        );
 if (app()->environment('production')) {
        URL::forceScheme('https'); // Force HTTPS
    }
     
DB::listen(function ($query) {
    if ($query->time > 200) {
        \Log::info("⏱️ Slow Query: {$query->sql} ({$query->time} ms)");
    }
});        
    }
}
