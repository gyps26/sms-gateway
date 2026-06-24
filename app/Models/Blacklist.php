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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $mobile_number
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\BlacklistFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Blacklist whereUserId($value)
 * @mixin \Eloquent
 */
class Blacklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer'
        ];
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blacklist';
}
