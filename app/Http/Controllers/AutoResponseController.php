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
use App\Http\Requests\StoreAutoResponseRequest;
use App\Http\Requests\UpdateAutoResponseRequest;
use App\Http\Resources\AutoResponseResource;
use App\Models\AutoResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class AutoResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        return DataTable::make(Auth::user()->autoResponses()->with('media'))
                        ->search(function ($query, $search) {
                            $query->whereJsonContains('messages', $search)
                                  ->orWhere('response', 'like', "%{$search}%");
                        })
                        ->sort(['enabled', 'type', 'criterion'])
                        ->render('AutoResponder/Index', fn($data) => [
                            'autoResponses' => AutoResponseResource::collection($data())
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function store(StoreAutoResponseRequest $request): RedirectResponse
    {
        $autoResponse = Auth::user()->autoResponses()->create($request->safe()->except('attachments'));

        foreach ($request->file('attachments', []) as $file) {
            $autoResponse->addMedia($file)->toMediaCollection('attachments');
        }

        return Redirect::back()->with('success', __('messages.auto_response.created'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\MediaCannotBeDeleted
     */
    public function update(UpdateAutoResponseRequest $request, AutoResponse $autoResponse): RedirectResponse
    {
        $medias = $autoResponse->getMedia('attachments');
        foreach ($medias as $media) {
            $exist = Arr::first($request->input('uploaded', []), fn($attachment) => data_get($attachment, 'id') === $media->id);

            if ($exist) {
                continue;
            }

            $autoResponse->deleteMedia($media);
        }

        $autoResponse->update($request->safe()->except(['attachments', 'uploaded']));

        foreach ($request->file('attachments', []) as $file) {
            $autoResponse->addMedia($file)->toMediaCollection('attachments');
        }

        return Redirect::back()->with('success', __('messages.auto_response.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(AutoResponse $autoResponse): RedirectResponse
    {
        Gate::authorize('delete', $autoResponse);

        $autoResponse->delete();

        return Redirect::back()->with('success', __('messages.auto_response.deleted'));
    }
}
