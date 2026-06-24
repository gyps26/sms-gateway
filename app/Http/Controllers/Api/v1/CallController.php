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

namespace App\Http\Controllers\Api\v1;

use App\Data\Filters\CallFiltersData;
use App\Http\Controllers\Controller;
use App\Http\Resources\CallResource;
use App\Models\Call;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Call Log', description: 'Call Log')]
class CallController extends Controller
{
    /**
     * List all calls
     *
     * Returns a list of calls. The calls are returned sorted by creation date, with the most recently created call appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(CallResource::class, Call::class, collection: true, paginate: 100)]
    public function index(CallFiltersData $filters): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Call::class);

        $calls = Auth::user()->calls()->filter($filters)->latest('started_at')->paginate(100)->withQueryString();

        return CallResource::collection($calls);
    }

    /**
     * Delete a call
     *
     * Permanently deletes a call.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Call $call): JsonResponse
    {
        Gate::authorize('delete', $call);

        $call->delete();

        return new JsonResponse(null, 204);
    }
}
