<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        Sanctum::getAccessTokenFromRequestUsing(function (Request $request) {

            $token = $request->cookie('access_token');

            if ($token) {
                return urldecode($token);
            }

            return $request->bearerToken();
        });
    }
}
