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

use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([SettingObserver::class])]
/**
 * @property int $id
 * @property string $name
 * @property array<array-key, mixed>|null $value
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'json',
            'user_id' => 'integer',
        ];
    }

    public static function retrieve(string $name, ?int $userId = null, $default = null): mixed
    {
        $settings = Setting::where('user_id', $userId)
                           ->unless($name === '*', fn($q) => $q->where('name', 'like', "$name%"))
                           ->get();

        if ($settings->count() > 1 || ($settings->count() === 1 && $settings->first()->name !== $name)) {
            $collection = $settings->mapWithKeys(fn($setting) => [$setting->name => $setting->value])
                                   ->undot()
                                   ->toArray();

            return $name === '*' ? $collection : data_get($collection, $name, []);
        } else if ($settings->count() > 0) {
            return $settings->first()->value;
        } else {
            return $default;
        }
    }

    public static function store(string $name, mixed $value, ?int $userId = null): void
    {
        Setting::updateOrCreate(
            ['name' => $name, 'user_id' => $userId],
            ['value' => $value]
        );
    }
}
