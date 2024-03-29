<?php

namespace Azuriom\Plugin\CloudflareSupport\Middleware;

use Azuriom\Http\Middleware\TrustProxies;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustCloudflare extends TrustProxies
{
    /**
     * The trusted proxies for the application.
     *
     * The IP ranges can be found on https://www.cloudflare.com/ips/
     *
     * @var array
     */
    protected $proxies = [
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '108.162.192.0/18',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // We use CF-Connecting-IP instead of X-Forwarded-For since
        // it has a consistent format containing only one IP.
        $request->headers->set('X-Forwarded-For', $request->header('CF-Connecting-IP'));

        Request::setTrustedProxies($this->proxies, $this->headers);

        if (! $request->secure()) {
            $this->setProtocolForRequest($request);
        }

        return $next($request);
    }

    protected function setProtocolForRequest(Request $request): void
    {
        $cfVisitorHeader = $request->header('CF-Visitor');

        if ($cfVisitorHeader === null) {
            return;
        }

        $cfVisitor = json_decode($cfVisitorHeader);

        if (! isset($cfVisitor->scheme)) {
            return;
        }

        // Some hosts replace the X-Forwarded-Proto and X-Forwarded-Port
        // headers, and they are not valid. To prevent this we set
        // these headers using the Cloudflare values.
        $request->headers->add([
            'X-Forwarded-Proto' => $cfVisitor->scheme,
            'X-Forwarded-Port' => $cfVisitor->scheme === 'https' ? 443 : 80,
        ]);

        // Some web hosts replace the REMOTE_ADDR with the X-Forwarded-For header
        // and the request is not considered as coming from a trusted proxy...
        if ($cfVisitor->scheme === 'https' && ! $request->secure()) {
            $request->server->set('HTTPS', 'on');
        }
    }
}
