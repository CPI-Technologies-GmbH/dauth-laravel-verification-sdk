<?php

namespace DAuth\ChallengeSDK\Providers;

use DAuth\ChallengeSDK\Commands\Instances\GetIPFSInstance;
use Illuminate\Support\ServiceProvider;

/**
 * Class BaseServiceProvider
 * @package Nice\Kyc
 */
class BaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/abis' => resource_path('abis'),
            __DIR__ . '../config/dauth.php' => config_path('dauth.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GetIPFSInstance::class);
    }
}
