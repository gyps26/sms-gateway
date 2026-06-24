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

namespace App\Docs\Strategies;

use Illuminate\Routing\Route;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\ParamHelpers;
use Knuckles\Scribe\Extracting\ParsesValidationRules;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionProperty;
use ReflectionUnionType;
use Spatie\LaravelData\Data;

class GetFromLaravelDataBase extends Strategy
{
    /**
     * Trait containing some helper methods for dealing with "parameters",
     * such as generating examples and casting values to types.
     * Useful if your strategy extracts information about parameters or generates examples.
     */
    use ParamHelpers;
    use ParsesValidationRules;

    protected string $customParameterDataMethodName = '';

    /**
     * @link https://scribe.knuckles.wtf/laravel/advanced/plugins
     *
     * @param  ExtractedEndpointData  $endpointData  The endpoint we are currently processing.
     *   Contains details about httpMethods, controller, method, route, url, etc., as well as already extracted data.
     * @param  array  $settings  Settings to be applied to this strategy
     *
     * See the documentation linked above for more details about writing custom strategies.
     *
     * @return array|null
     * @throws \ReflectionException
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): ?array
    {
        return $this->getParametersFromLaravelData($endpointData->method, $endpointData->route);
    }

    /**
     * @throws \ReflectionException
     */
    private function getParametersFromLaravelData(ReflectionFunctionAbstract $method, Route $route): array
    {
        if (!$laravelDataReflectionClass = $this->getLaravelDataReflectionClass($method)) {
            return [];
        }

        if (!$this->isLaravelDataMeantForThisStrategy($laravelDataReflectionClass)) {
            return [];
        }

        $className = $laravelDataReflectionClass->getName();

        $laravelData = (new ReflectionClass($className))->newInstanceWithoutConstructor();

        $parametersFromLaravelData = $this->getParametersFromValidationRules(
            $this->getRouteValidationRules($laravelData),
            $this->getCustomParameterData($laravelData)
        );

        return $this->normaliseArrayAndObjectParameters($parametersFromLaravelData);
    }

    protected function getRouteValidationRules(Data $data)
    {
        if (method_exists($data, 'getValidationRules')) {
            $reflect = new ReflectionClass($data);
            $properties = [];
            foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $properties[$property->getName()] = $this->generateDummyValue($property->getType());
            }
            return app()->call([$data, 'getValidationRules'], ['payload' => $properties]);
        }

        return [];
    }

    protected function getCustomParameterData(Data $data)
    {
        if (method_exists($data, $this->customParameterDataMethodName)) {
            return call_user_func_array([$data, $this->customParameterDataMethodName], []);
        }

        c::warn("No {$this->customParameterDataMethodName}() method found in " . get_class($data) . ". Scribe will only be able to extract basic information from the rules() method.");

        return [];
    }

    protected function getMissingCustomDataMessage($parameterName): string
    {
        return "No data found for parameter '$parameterName' in your {$this->customParameterDataMethodName}() method. Add an entry for '$parameterName' so you can add a description and example.";
    }

    protected function getLaravelDataReflectionClass(ReflectionFunctionAbstract $method): ?ReflectionClass
    {
        foreach ($method->getParameters() as $argument) {
            $argType = $argument->getType();
            if ($argType === null || $argType instanceof ReflectionUnionType) continue;

            $argumentClassName = $argType->getName();

            if (!class_exists($argumentClassName)) continue;

            try {
                $argumentClass = new ReflectionClass($argumentClassName);
            } catch (ReflectionException $e) {
                continue;
            }

            if (
                (class_exists(Data::class) && $argumentClass->isSubclassOf(Data::class))) {
                return $argumentClass;
            }
        }

        return null;
    }

    protected function isLaravelDataMeantForThisStrategy(ReflectionClass $laravelDataReflectionClass): bool
    {
        return $laravelDataReflectionClass->hasMethod($this->customParameterDataMethodName);
    }
}
