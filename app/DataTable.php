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

namespace App;

use App\Data\PaginationData;
use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DataTable
{

    private Builder $builder;

    private array $validated;

    private array $data;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->validated = [];
        $this->data = Request::all();
    }

    public function usingData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  array<int, string>  $fields
     * @param  array<string, string>  $mappings
     */
    public function sort(array $fields, array $mappings = [], string $default = 'created_at'): static
    {
        $this->validated += Validator::make(Arr::only($this->data, ['sort_by', 'sort_direction']), [
            'sort_by' => ['required_with:sort_direction', Rule::in($fields)],
            'sort_direction' => ['nullable', Rule::in(['asc', 'desc'])]
        ])->valid();

        $this->builder = $this->builder->when(Arr::has($this->validated, ['sort_by']), function ($query) use ($mappings) {
            $field = transform($this->validated['sort_by'], fn($sortBy) => $mappings[$sortBy] ?? $sortBy);
            $query->orderBy($field, $this->validated['sort_direction'] ?? 'desc');
        }, function ($query) use ($default, $fields, $mappings) {
            $query->latest($default);
            if (in_array($default, $fields) || ($default = array_search($default, $mappings))) {
                $this->validated['sort_by'] = $default;
                $this->validated['sort_direction'] = 'desc';
            }
        });

        return $this;
    }

    public static function make(Builder $builder): DataTable
    {
        return new DataTable($builder);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function query(string $primaryKey = 'id'): Builder
    {
        Validator::make($this->data, [
            'ids' => ['array'],
            'ids.*' => ['integer', 'min:1', 'distinct'],
            'all' => ['boolean'],
        ])->validate();

        return $this->builder
            ->unless(data_get($this->data, 'all'), fn($query) => $query->whereIn($primaryKey, data_get($this->data, 'ids', [])))
            ->clone();
    }

    /**
     * @param  \Closure(\Illuminate\Contracts\Database\Query\Builder): (\Illuminate\Contracts\Database\Query\Builder)  $closure
     */
    public function search(Closure $closure): static
    {
        $this->validated += Validator::make(Arr::only($this->data, ['search']), [
            'search' => ['string'],
        ])->valid();

        $this->builder
            ->when(isset($this->validated['search']), fn($query) => $query->where(fn($query) => $closure($query, $this->validated['search'])));

        return $this;
    }

    /**
     * @param  Closure(Closure): array<string, mixed>  $closure
     */
    public function render(string $component, Closure $closure): Response|RedirectResponse
    {
        $this->validated += PaginationData::validateAndCreate($this->data)->toArray();

        $paginator = function () {
            $paginator = $this->builder->paginate($this->validated['per_page'])->withQueryString();

            abort_if($this->validated['page'] > $paginator->lastPage(), Redirect::to($paginator->url($paginator->lastPage())));

            return $paginator;
        };

        return Inertia::render($component, array_merge_recursive(['params' => $this->validated], $closure($paginator)));
    }
}
