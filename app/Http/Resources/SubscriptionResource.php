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

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Subscription */
class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'user_id' => $this->user_id,
            'subscription_id' => $this->subscription_id,
            'payment_method' => $this->payment_method,
            'billing_info' => $this->billing_info,
            'features' => $this->features,
            'taxes' => $this->taxes,
            'status' => $this->status,
            'recurring' => $this->recurring,
            'renewal_at' => $this->renewal_at,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
            'coupon' => CouponResource::make($this->whenLoaded('coupon')),
            'plan' => PlanResource::make($this->whenLoaded('plan')),
        ];
    }
}
