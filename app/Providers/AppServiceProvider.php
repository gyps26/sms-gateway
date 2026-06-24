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

namespace App\Providers;

use App\Helpers\Sms;
use App\JwtAuth;
use App\Models\AutoResponse;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\Message;
use App\Models\PaymentGateway;
use App\Models\PersonalAccessToken;
use App\Models\SenderId;
use App\Models\SendingServer;
use App\Models\Sim;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Knuckles\Scribe\Scribe;
use Laravel\Sanctum\Sanctum;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind('jwt-auth', fn() => new JwtAuth());
        $this->app->bind('sms', fn() => new Sms());

        if (app()->runningInConsole() === false) {
            $currentUrl = request()->getSchemeAndHttpHost();
            config(['app.url' => $currentUrl]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Relation::enforceMorphMap([
            'sending_server' => SendingServer::class,
            'sender_id' => SenderId::class,
            'device' => Device::class,
            'sim' => Sim::class,
            'media' => Media::class,
            'campaign' => Campaign::class,
            'auto_response' => AutoResponse::class,
            'message' => Message::class,
            'user' => User::class
        ]);

        Auth::viaRequest('jwt', function (Request $request) {
            $token = $request->bearerToken();
            if (is_null($token)) {
                return null;
            }

            $subject = (new JwtAuth())->parseToken($token);
            if (is_null($subject)) {
                return null;
            }

            $device = Device::find($subject);

            return $device?->owner;
        });

        // Check if any of the keys in the array have the given value
        Arr::macro('any', function (array $array, mixed $value): bool {
            foreach ($array as $key => $val) {
                if ($value instanceof Closure) {
                    if ($value($val, $key)) {
                        return true;
                    }
                } elseif ($value === $val) {
                    return true;
                }
            }
            return false;
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        JsonResource::withoutWrapping();

        HeadingRowFormatter::extend('custom', fn($value, $key) => Str::slug($value, '_'));

        Route::bind('payment_gateway', fn(string $value) => PaymentGateway::find($value) ?? abort(404));

        if (class_exists(Scribe::class)) {
            Scribe::beforeResponseCall(fn() => ($user = User::first()) ? Auth::login($user) : null);
        }
    }
}
