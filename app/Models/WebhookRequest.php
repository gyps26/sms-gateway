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

namespace App\Models;

use Spatie\WebhookClient\Models\WebhookCall as SpatieWebhookCall;

/**
 * @property int $id
 * @property string $name
 * @property string $url
 * @property array<array-key, mixed>|null $headers
 * @property array<array-key, mixed>|null $payload
 * @property array<array-key, mixed>|null $exception
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebhookRequest whereUrl($value)
 * @mixin \Eloquent
 */
class WebhookRequest extends SpatieWebhookCall
{
}
