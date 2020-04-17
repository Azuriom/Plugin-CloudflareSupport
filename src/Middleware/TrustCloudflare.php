<?php

namespace Azuriom\Plugin\CloudflareSupport\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustCloudflare
{
    /**
     * The trusted proxies for the application.
     *
     * The IP ranges can be found on https://www.cloudflare.com/ips/
     *
     * @var array
     */
    protected $proxies = [
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/12',
        '172.64.0.0/13',
        '131.0.72.0/22',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2a06:98c0::/29',
        '2c0f:f248::/32',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // We use CF-Connecting-IP instead of X-Forwarded-For since
        // it has a consistent format containing only one IP.
        $request->headers->set('X-Forwarded-For', $request->header('CF-Connecting-IP'));

        Request::setTrustedProxies($this->proxies, Request::HEADER_X_FORWARDED_ALL);

        return $next($request);
    }
}
