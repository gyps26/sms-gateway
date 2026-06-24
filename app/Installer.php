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

use App\Enums\IntervalUnit;
use App\Helpers\Common;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Installer
{
    public array $dependencies;

    public array $permissions;

    public function __construct()
    {
        $this->dependencies = [
            'PHP 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
            'Ctype' => extension_loaded('ctype'),
            'cURL' => extension_loaded('curl'),
            'DOM' => extension_loaded('dom'),
            'Fileinfo' => extension_loaded('fileinfo'),
            'Filter' => extension_loaded('filter'),
            'GD' => extension_loaded('gd'),
            'Hash' => extension_loaded('hash'),
            'iconv' => extension_loaded('iconv'),
            'intl' => extension_loaded('intl'),
            'JSON' => extension_loaded('json'),
            'Mbstring' => extension_loaded('mbstring'),
            'OpenSSL' => extension_loaded('openssl'),
            'PCRE' => extension_loaded('pcre'),
            'PDO' => extension_loaded('pdo'),
            'Session' => extension_loaded('session'),
            'SimpleXML' => extension_loaded('simplexml'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'XML' => extension_loaded('xml'),
            'XMLReader' => extension_loaded('xmlreader'),
            'Zip' => extension_loaded('zip'),
            'Zlib' => extension_loaded('zlib'),
        ];

        $files = [
            '.env',
        ];

        $directories = [
            'bootstrap/cache',
            'public',
            'storage',
            'storage/app/private',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($files as $file) {
            $this->permissions[$file] = transform(base_path($file), fn($file) => is_writable($file) && is_file($file));
        }

        foreach ($directories as $directory) {
            $this->permissions[$directory] = transform(base_path($directory), fn($directory) => is_writable($directory) && is_dir($directory));
        }
    }

    /**
     * @param  array<string, string>  $data
     *
     * @throws \Exception
     */
    public function createConfig(array $data): void
    {
        $env = [
            'APP_NAME' => 'SMS Gateway',
            'APP_ENV' => 'production',
            'APP_KEY' => config('app.key') ?: 'base64:' . base64_encode(Str::random(32)),
            'APP_DEBUG' => config('app.debug') ? 'true' : 'false',
            'APP_TIMEZONE' => config('app.timezone'),
            'APP_URL' => config('app.url'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['host'],
            'DB_PORT' => $data['port'],
            'DB_DATABASE' => $data['name'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'],
            'DB_PREFIX' => config('database.connections.mysql.prefix'),
            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => '120',
            'SESSION_EXPIRE_ON_CLOSE' => 'false',
            'QUEUE_CONNECTION' => 'database',
            'CACHE_STORE' => 'file',
            'MIX_PUSHER_APP_KEY' => '${PUSHER_APP_KEY}',
            'MIX_PUSHER_APP_CLUSTER' => '${PUSHER_APP_CLUSTER}',
            'TELESCOPE_ENABLED' => 'false',
            'MAIL_FROM_NAME' => '${APP_NAME}',
            'VITE_APP_NAME' => '${APP_NAME}',
        ];

        $envPath = base_path('.env');

        $tmpPath = $envPath . '.tmp.' . uniqid();
        $result = file_put_contents($tmpPath, Common::toEnv($env));
        if ($result !== false) {
            $result = rename($tmpPath, $envPath);
        }

        if ($result) {
            $publicPath = dirname($envPath) . '/public/storage';
            if (! file_exists($publicPath)) {
                $storagePath = realpath(dirname($envPath) . '/storage/app/public');
                if ($storagePath) {
                    symlink($storagePath, $publicPath);
                }
            }
        } else {
            throw new Exception('Failed to write to .env file. Please ensure the file is writable.');
        }

        $this->runMigrations();
    }

    public function runMigrations(): void
    {
        if (Schema::hasTable('migrations') && DB::table('migrations')->count()) {
            return;
        }

        $migrator = app('migrator');
        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
        }
        $migrator->run(database_path('migrations'));
    }

    public function createAdmin(array $data): void
    {
        $this->runMigrations();

        $plan = Plan::create([
            'name' => 'Trial',
            'description' => 'The trial plan for new users.',
            'price' => 0,
            'currency' => 'USD',
            'interval' => 1,
            'interval_unit' => IntervalUnit::Month,
            'features' => [
                'credits' => 200,
                'contact_lists' => 1,
                'contacts' => 200,
                'devices' => 2,
                'sending_servers' => 0,
                'sender_ids' => 0,
                'templates' => 5,
                'webhooks' => 1,
                'api_tokens' => 1,
                'auto_responses' => 5,
                'data_export' => true,
            ],
            'position' => 1,
            'enabled' => false
        ]);

        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 4]);

        DB::table('users')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hash,
            'is_admin' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Setting::store('app.support_url', "mailto:{$data['email']}");
        Setting::store('saas.trial.plan_id', $plan->id);
        Setting::store('app.license_code', $data['license_code']);

        $envPath = base_path('.env');
        if ($config = file_get_contents($envPath)) {
            $config = str_replace('SESSION_DRIVER=file', 'SESSION_DRIVER=database', $config);
            $tmpPath = $envPath . '.tmp.' . uniqid();
            if (file_put_contents($tmpPath, $config) !== false) {
                rename($tmpPath, $envPath);
            }
        }
    }

    public function getDbConfig(): array
    {
        return [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'name' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];
    }

    public function markInstalled(): bool
    {
        return file_put_contents(storage_path('installed'), '') !== false && File::delete(storage_path('update'));
    }
}
