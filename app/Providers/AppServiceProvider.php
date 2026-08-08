<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $gym = config('gym');

        if (! empty($gym['app_name'])) {
            config(['app.name' => $gym['app_name']]);
        }

        View::composer('*', function ($view) use ($gym) {
            $view->with('gym', $gym);
        });
    }
}
