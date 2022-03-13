<?php

namespace Azuriom\Plugin\CloudflareSupport\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;

class CloudflareSupportServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     *
     * @var array
     */
    protected array $middleware = [
        \Azuriom\Plugin\CloudflareSupport\Middleware\TrustCloudflare::class,
    ];

    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        $this->middleware($this->middleware, true);
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
