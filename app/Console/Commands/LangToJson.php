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

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class LangToJson extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:transform {--delete : Delete PHP files after conversion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert PHP language files to JSON';

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        $this->info('Converting PHP language files to JSON...');

        $directories = File::directories(lang_path());

        foreach ($directories as $directory) {
            $lang = basename($directory);
            $files = File::files($directory);

            $result = [];
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $array = Arr::dot(require $file->getPathname());
                    $array = Arr::prependKeysWith($array, $file->getBasename('.php') . '.');
                    $result += $array;
                }
            }

            ksort($result);

            $json = lang_path("{$lang}.json");

            if (File::exists($json)) {
                $existing = json_decode(File::get($json), true);
                $result = array_merge($existing, $result);
            }

            File::put($json, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->info("Created JSON file for language: {$lang}");

            if ($this->option('delete')) {
                File::deleteDirectory($directory);
                $this->info("Deleted PHP files for language: {$lang}");
            }
        }

        $this->info('All done!');
    }
}
