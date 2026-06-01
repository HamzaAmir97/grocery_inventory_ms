<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('viewDashboard', fn (User $user): bool => $user->isAdministrator());

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $key = optional($request->user('api'))->id ?: $request->ip();

            return Limit::perMinute(60)->by((string) $key);
        });

        RateLimiter::for('login', function (Request $request) {
            $email = strtolower((string) $request->input('email', ''));
            $ip = (string) $request->ip();

            return [
                Limit::perMinute(5)->by($email.'|'.$ip),
                Limit::perMinute(20)->by($ip),
            ];
        });
    }
}
