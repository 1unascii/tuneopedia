<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloudflare sits between users and our Apache server as a reverse proxy.
        // Apache only sees HTTP on port 80, but users connect via HTTPS through Cloudflare.
        // Without this, Laravel generates HTTP URLs (not HTTPS) because it doesn't know
        // the original request was HTTPS — causing "mixed content" browser errors
        // where the page loads over HTTPS but JS fetch calls go to HTTP and get blocked.
        //
        // '*' trusts all proxies (safe here because Cloudflare is our only entry point).
        // The headers tell Laravel which X-Forwarded-* headers to read from Cloudflare
        // so it can detect the real protocol (HTTPS), host, port, and client IP.
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
