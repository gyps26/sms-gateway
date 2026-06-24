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

use App\Contracts\MessageGateway as MessageGatewayContract;
use App\Helpers\Common;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MessageGateway
{
    public string $name;

    public string $class;

    public string $driver;

    public array $fields;

    public array $supported_types;

    public static function all(): Collection
    {
        $namespace = app()->getNamespace() . 'MessageGateways';

        $classes = Common::getClasses(Str::lcfirst($namespace));

        $drivers = new Collection();
        foreach ($classes as $class) {
            $class = "$namespace\\$class";
            $implements = class_implements($class);
            if (in_array(MessageGatewayContract::class, $implements)) {
                $gateway = new MessageGateway();
                /** @var MessageGatewayContract $class */
                $gateway->name = $class::name();
                $gateway->driver = Str::after($class, "$namespace\\");
                $gateway->class = $class;
                $gateway->fields = $class::fields();
                $gateway->supported_types = $class::supportedTypes();
                $drivers->put($gateway->driver, $gateway);
            }
        }

        return $drivers;
    }

    public static function find(string $driver): MessageGateway
    {
        return static::all()->get($driver);
    }
}
