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

use App\DataTable;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Rules\Coupon as CouponRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(): Response|RedirectResponse
    {
        Gate::authorize('viewAny', Coupon::class);

        return DataTable::make(Coupon::query())
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%")
                                  ->orWhere('code', $search);
                        })
                        ->sort(['name', 'code', 'discount', 'quantity', 'used', 'expires_at', 'enabled', 'updated_at'])
                        ->render('Coupons/Index', fn($data) => [
                            'coupons' => CouponResource::collection($data()),
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::create($request->only(['name', 'code', 'discount', 'quantity', 'expires_at', 'enabled']));

        return Redirect::back()->with('success', __('messages.coupon.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->only(['name', 'code', 'quantity', 'expires_at', 'enabled']));

        return Redirect::back()->with('success', __('messages.coupon.updated'));
    }

    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'coupon' => ['required', new CouponRule()],
        ]);

        $coupon = Coupon::whereCode($request->input('coupon'))->first();

        return new JsonResponse(['discount' => $coupon->discount]);
    }
}
