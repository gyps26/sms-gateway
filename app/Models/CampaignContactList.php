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

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $campaign_id
 * @property int $contact_list_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList whereContactListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CampaignContactList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignContactList extends Pivot
{
    public function casts(): array
    {
        return [
            'campaign_id' => 'integer',
            'contact_list_id' => 'integer',
        ];
    }
}
