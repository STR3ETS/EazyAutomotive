<?php

namespace App\Providers;

use App\Services\Rdw\Orv\OrvClient;
use App\Services\Rdw\Orv\SandboxOrvClient;
use App\Services\Rdw\Orv\SoapOrvClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // RDW ORV: kies de sandbox (lokaal simuleren) of de echte SOAP-webservice.
        $this->app->bind(OrvClient::class, function () {
            $config = config('services.rdw.orv', []);

            return ($config['mode'] ?? 'sandbox') === 'soap'
                ? new SoapOrvClient($config)
                : new SandboxOrvClient();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
