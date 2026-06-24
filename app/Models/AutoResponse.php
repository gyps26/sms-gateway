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

use App\Enums\Criterion;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property array<array-key, mixed> $messages
 * @property string $response
 * @property string $type
 * @property Criterion $criterion
 * @property bool $enabled
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\AutoResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereCriterion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoResponse whereUserId($value)
 * @mixin \Eloquent
 */
class AutoResponse extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'criterion' => Criterion::class,
            'messages' => 'array',
            'enabled' => 'boolean',
            'user_id' => 'integer'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matches(string $value): bool
    {
        foreach ($this->messages as $message) {
            try {
                $result = match ($this->criterion) {
                    Criterion::Match => mb_strtolower($value) === mb_strtolower($message) || mb_strtoupper($value) === mb_strtoupper($message),
                    Criterion::MatchCase => $value === $message,
                    Criterion::Contains => Str::contains($value, $message),
                    Criterion::Regex => preg_match('/' . $message . '/', $value, $matches) === 1 && $matches[0] === $value
                };

                if ($result) {
                    return true;
                }
            } catch (Exception) {
                continue;
            }
        }
        return false;
    }
}
