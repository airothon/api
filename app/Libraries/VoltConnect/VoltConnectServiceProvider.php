<?php

namespace App\Libraries\VoltConnect;

use Illuminate\Support\ServiceProvider;

class VoltConnectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('airothon', function () {
            return new VoltConnect();
        });
    }

    public function boot(): void
    {
        //
    }
}
