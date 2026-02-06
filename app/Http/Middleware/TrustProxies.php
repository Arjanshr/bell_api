<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request; // ← IMPORTANT: use Symfony's Request

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // Trust all proxies, or specify IPs if needed

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = 
        \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR |
        \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST |
        \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT |
        \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO;
}
