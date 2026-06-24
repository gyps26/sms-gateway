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

namespace App\Docs\Strategies\BodyParameters;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Knuckles\Scribe\Extracting\Shared\UrlParamsNormalizer;
use Knuckles\Scribe\Extracting\Strategies\BodyParameters\GetFromFormRequest as GetFromFormRequestBase;
use ReflectionClass;

class GetFromFormRequest extends GetFromFormRequestBase
{
    /**
     * @throws \ReflectionException
     */
    protected function getRouteValidationRules($formRequest)
    {
        if (method_exists($formRequest, 'validator')) {
            $validationFactory = app(ValidationFactory::class);

            return app()->call([$formRequest, 'validator'], [$validationFactory])
                        ->getRules();
        } elseif (method_exists($formRequest, 'rules')) {
            $reflector = new ReflectionClass($formRequest);
            $method = $reflector->getMethod('rules');
            $typeHintedEloquentModels = UrlParamsNormalizer::getTypeHintedEloquentModels($method);
            $parameters = array_map(function ($model) { return $model::first(); }, $typeHintedEloquentModels);
            return app()->call([$formRequest, 'rules'], $parameters);
        }

        return [];
    }
}
