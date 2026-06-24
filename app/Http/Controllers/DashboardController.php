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

namespace App\Http\Controllers;

use App\Enums\CallType;
use App\Enums\CampaignStatus;
use App\Enums\MessageStatus;
use App\Enums\PaymentStatus;
use App\Enums\UssdPullStatus;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Models\UssdPull;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, #[CurrentUser] $user): Response
    {
        $stats = Setting::retrieve('app.dashboard.stats', $user->id, []) + config('app.dashboard.stats');

        $counts = [];

        foreach (array_filter($stats) as $stat => $enabled) {
            $counts[$stat] = match ($stat) {
                'calls' => $this->countByEnum(Call::class, CallType::class, 'type'),
                'campaigns' => $this->countByEnum(Campaign::class, CampaignStatus::class),
                'messages' => $this->countByEnum(Message::class, MessageStatus::class),
                'ussd_pulls' => $this->countByEnum(UssdPull::class, UssdPullStatus::class)
            };
        }

        if ($user->is_admin) {
            $counts['misc'] = [
                'users' => User::whereIsAdmin(false)->count(),
                'earnings' => Payment::whereStatus(PaymentStatus::Completed)->sum('amount'),
            ];
        }

        $subscription = $user->currentSubscription;

        $welcome = $user->canConsume(1, 'devices')->allowed()
            && $user->canConsume(1, 'sender_ids')->allowed()
            && $user->devices()->count() === 0
            && $user->senderIds()->count() === 0;

        return Inertia::render('Dashboard', [
            'announcement' => config('app.announcements.member'),
            'realtime' => config('app.dashboard.realtime') && Setting::retrieve('app.dashboard.realtime', $user->id, false),
            'counts' => $counts,
            'credits' => config('saas.credits'),
            'welcome' => $welcome,
            'subscription' => $subscription?->only(['features', 'renewal_at', 'ends_at']),
        ]);
    }

    private function countByEnum(string $modelClass, string $enumClass, string $column = 'status'): Collection
    {
        return $modelClass::select([$column, DB::raw('count(*) as count')])
                          ->unless(Auth::user()->is_admin, fn($query) => $query->where('user_id', Auth::id()))
                          ->groupBy($column)
                          ->get()
                          ->mapWithKeys(fn($item) => [Str::slug($item->{$column}->value, '_') => $item->count]);
    }
}
