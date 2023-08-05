<?php

namespace Azuriom\Plugin\CloudflareSupport\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Http\Middleware\TrustProxies;
use Azuriom\Plugin\CloudflareSupport\Middleware\TrustCloudflare;

class CloudflareSupportServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->app->bind(TrustProxies::class, TrustCloudflare::class);
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        //
    }
}
