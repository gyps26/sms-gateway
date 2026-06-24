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

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonSerializable;
use Psr\Log\LoggerInterface;

/**
 * @property int total
 * @property int processed
 * @property int failures
 * @property bool $cancelled
 * @property string $status
 * @property string $logfile
 */
class Monitor implements JsonSerializable
{
    private string $id;
    private array $data = ['total', 'processed', 'failures', 'cancelled', 'status', 'logfile'];

    public function __construct(string $name, int $id)
    {
        $this->id = Str::afterLast($name, '\\') . '-' . $id;
        if (is_null($this->logfile)) {
            $this->logfile = 'logs' . DIRECTORY_SEPARATOR . $this->id . '_' . uniqid() . '.log';
        }
    }

    public static function for(string $name, int $id): Monitor
    {
        return new Monitor($name, $id);
    }

    public function __get(string $name)
    {
        if (in_array($name, $this->data)) {
            return Cache::get($this->getCacheKey($name));
        }
        $trace = debug_backtrace();
        trigger_error("Undefined property via __get(): {$name} in {$trace[0]['file']} on line {$trace[0]['line']}");
        return null;
    }

    public function __set(string $name, $value): void
    {
        if (in_array($name, $this->data)) {
            Cache::forever($this->getCacheKey($name), $value);
        }
    }

    private function getCacheKey(string $data): string
    {
        return "{$this->id}_{$data}";
    }

    public function __isset(string $name): bool
    {
        if (in_array($name, $this->data)) {
            return Cache::has($this->getCacheKey($name));
        }
        return false;
    }

    public function __unset($name)
    {
        if (in_array($name, $this->data)) {
            Cache::forget($this->getCacheKey($name));
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'cancelled' => $this->cancelled,
            'failures' => $this->failures,
            'processed' => $this->processed,
            'total' => $this->total,
            'status' => $this->status,
            'progress' => $this->progress()
        ];
    }

    public function progress(): ?float
    {
        return isset($this->processed) && $this->total ?
            (float)number_format(($this->processed * 100) / $this->total, 2) : null;
    }

    public function logger(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => Storage::path($this->logfile),
        ]);
    }

    public function isRunning(): bool
    {
        return $this->total > 0 && $this->processed !== $this->total;
    }

    public function isFinished(): bool
    {
        return $this->processed === $this->total;
    }

    public function cancel(): bool
    {
        if ($this->isRunning()) {
            $this->cancelled = true;
            return true;
        }

        return false;
    }

    public function clear(): void
    {
        if ($this->logfile && Storage::exists($this->logfile)) {
            Storage::delete($this->logfile);
        }

        foreach ($this->data as $property) {
            unset($this->{$property});
        }
    }
}
