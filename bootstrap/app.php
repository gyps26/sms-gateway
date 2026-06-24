<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

use App\Console\Commands\LangToJson;
use App\Exceptions\CancellationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        LangToJson::class
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'not-installed' => \App\Http\Middleware\NotInstalled::class,
            'installed' => \App\Http\Middleware\Installed::class,
            'updated' => \App\Http\Middleware\Updated::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LanguageSwitcher::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ], prepend: [
            \App\Http\Middleware\App::class,
        ], replace: [
            \Illuminate\Session\Middleware\StartSession::class => \App\Http\Middleware\StartInertiaSession::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\LanguageSwitcher::class,
        ]);

        $middleware->encryptCookies([
            'lang',
            'token',
            'device_id'
        ]);

        $middleware->trustProxies('*');

        $middleware->validateCsrfTokens(except: [
            '*/webhook'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (CancellationException $e) {
            return false;
        });

        $exceptions->shouldRenderJsonWhen(fn($request, $e) => $request->expectsJson() || $request->is('api/*'))
                   ->respond(function (Response $response, Throwable $exception, Request $request) {
                       if (! $request->inertia()) {
                           return $response;
                       }

                       if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 401, 402, 404, 403, 429])) {
                           if ($request->method() === 'GET') {
                               $props = [
                                   'code' => $response->getStatusCode()
                               ];

                               if ($response->getStatusCode() === 403) {
                                   $props['message'] = $exception->getMessage() ?: null;
                               }

                               return Inertia::render('ErrorPage', $props)
                                             ->toResponse($request)
                                             ->setStatusCode($response->getStatusCode());
                           }

                           return back()->with([
                               'error' => when($exception->getMessage(), fn($message) => $message, (fn() => $this->statusText)->call($response)),
                           ]);
                       } elseif ($response->getStatusCode() === 419) {
                           return back()->with([
                               'error' => 'The page expired, please try again.',
                           ]);
                       }

                       return $response;
                   });
    })->create();
