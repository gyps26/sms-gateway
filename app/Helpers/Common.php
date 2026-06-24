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

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Common
{
    public static function getClasses(string $namespace): array
    {
        $namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $files = File::files(base_path($namespace));
        return array_map(
            callback: function ($file) use ($namespace) {
                return str_replace('.php', '', $file->getFilename());
            },
            array: $files
        );
    }

    /**
     * @return array<int, string>
     */
    public static function getAvailableLocales(): array
    {
        $availableLocales = [];

        // Check for JSON files
        foreach (File::files(lang_path()) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $availableLocales[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        // Check for language folders
        foreach (File::directories(lang_path()) as $directory) {
            $availableLocales[] = basename($directory);
        }

        return array_values(array_unique($availableLocales));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function toEnv(array $data): string
    {
        $lines = [];

        foreach ($data as $key => $value) {
            // Convert booleans to strings
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            // Convert other non-string scalars
            if (is_null($value)) {
                $value = '';
            } elseif (! is_string($value)) {
                $value = (string)$value;
            }

            // Check for characters that require quoting
            if (preg_match('/[\s=#$\'"\\\\]/u', $value)) {
                // Escape double quotes and backslashes inside quoted values
                $value = '"' . addcslashes($value, "\\\"\n\r\t") . '"';
            }

            $currentPrefix = strtoupper(strtok($key, '_'));
            if (isset($prevPrefix) && $currentPrefix !== $prevPrefix) {
                $lines[] = '';
            }
            $lines[] = strtoupper($key) . '=' . $value;
            $prevPrefix = $currentPrefix;
        }

        return implode(PHP_EOL, $lines);
    }

    public static function getTotalRows(string $filename): int
    {
        $spreadsheet = IOFactory::load($filename);
        return array_sum(Arr::map($spreadsheet->getAllSheets(), function (Worksheet $sheet) {
            return $sheet->getHighestDataRow() - 1;
        }));
    }

    /**
     * @see https://stackoverflow.com/a/13479855/1273550
     *
     * @param  array<string, string>  $data
     */
    public static function spintax(string $message, array $data = []): string
    {
        return preg_replace_callback("/{(.*?)}/", function ($match) use ($data) {
            // Splits 'foo|bar' strings into an array
            $words = explode("|", $match[1]);

            $count = count($words);
            if ($count > 1) {
                // Grabs a random array entry and returns it
                return $words[array_rand($words)];
            } elseif ($count > 0) {
                // In case of single word if there is data for it, then replace it with the value.
                return Arr::get($data, $words[0]) ?? $match[0];
            } else {
                return '';
            }
        }, $message);
    }

    public static function hasSpintax(string $message): bool
    {
        preg_match_all("/{(.*?)}/", $message, $matches);
        return count($matches[0]) > 0;
    }

    public static function isInstalled(): bool
    {
        $path = storage_path('installed');
        return file_exists($path) && is_file($path);
    }

    public static function isUpdatePending(): bool
    {
        $path = storage_path('update');
        return file_exists($path) && is_file($path);
    }

    public static function generateFavicon(string $path): void
    {
        Image::make($path)
             ->resize(192, 192)
             ->save(public_path('android-chrome-192x192.png'), '100', 'png');

        Image::make($path)
             ->resize(512, 512)
             ->save(public_path('android-chrome-512x512.png'), '100', 'png');

        Image::make($path)
             ->resize(180, 180)
             ->save(public_path('apple-touch-icon.png'), '100', 'png');

        Image::make($path)
             ->resize(16, 16)
             ->save(public_path('favicon-16x16.png'), '100', 'png');

        Image::make($path)
             ->resize(32, 32)
             ->save(public_path('favicon-32x32.png'), '100', 'png');

        $name = Setting::retrieve('app.name', default: config('app.name'));

        $manifest = [
            'name' => $name,
            'short_name' => $name,
            'icons' => [
                [
                    'src' => '/android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => '/android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ],
            ],
            'theme_color' => '#ffffff',
            'background_color' => '#ffffff',
            'display' => 'standalone'
        ];

        file_put_contents(public_path('site.webmanifest'), json_encode($manifest));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getTranslations(string $key, ?string $locale = null): array
    {
        $translations = Lang::get('ui', locale: $locale);
        if (is_array($translations)) {
            return Arr::dot($translations);
        }

        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        $filePath = lang_path("{$locale}.json");

        if (File::exists($filePath)) {
            $translations = json_decode(File::get($filePath), true);

            return collect($translations)
                ->filter(fn($v, $k) => str_starts_with($k, "{$key}."))
                ->mapWithKeys(fn($v, $k) => [str_replace("{$key}.", '', $k) => $v])
                ->toArray();
        }

        return [];
    }
}
