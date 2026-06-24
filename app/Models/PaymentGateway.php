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

use App\Contracts\PaymentGateway as PaymentGatewayContract;
use App\Helpers\Common;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaymentGateway
{

    public string $name;

    public string $label;

    public bool $enabled;

    public string $class;

    public array $fields;

    public array $config;

    public ?string $docs;

    public static function find(string $name): PaymentGateway|null
    {
        return static::all()->first(fn($paymentGateway) => $paymentGateway->name === $name);
    }

    public static function all(): Collection
    {
        $namespace = app()->getNamespace() . 'PaymentGateways';

        $classes = Common::getClasses(Str::lcfirst($namespace));

        $paymentGateways = new Collection();
        foreach ($classes as $class) {
            $class = "$namespace\\$class";
            $implements = class_implements($class);
            if (in_array(PaymentGatewayContract::class, $implements)) {
                $paymentGateway = new PaymentGateway();
                /** @var PaymentGatewayContract $class */
                $paymentGateway->label = $class::label();
                $paymentGateway->name = Str::after($class, "$namespace\\");
                $paymentGateway->class = $class;
                $paymentGateway->fields = $class::fields();

                $config = 'payment-gateways.' . $paymentGateway->name;
                $paymentGateway->config = Arr::mapWithKeys(
                    array: $paymentGateway->fields,
                    callback: function ($field, $key) use ($config, $paymentGateway) {
                        $value = config("{$config}.$key");
                        return [
                            $key => $field['type'] === 'boolean' ? (bool)$value : $value
                        ];
                    }
                );
                $paymentGateway->enabled = config("{$config}.enabled", false);
                $paymentGateway->docs = config("{$config}.docs");

                $paymentGateways->add($paymentGateway);
            }
        }
        return $paymentGateways;
    }
}
