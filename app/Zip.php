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

use Closure;
use Exception;
use FilesystemIterator;
use Illuminate\Support\Facades\File;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Zip extends ZipArchive
{
    /**
     * @throws \Exception
     */
    public static function directory(string $source, string $output): void
    {
        $zip = new Zip();
        File::ensureDirectoryExists(File::dirname($output));
        $zip->open($output, ZipArchive::CREATE);
        /** @var RecursiveDirectoryIterator $iterator */
        $iterator = new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator(directory: $source, flags: FilesystemIterator::FOLLOW_SYMLINKS),
            mode: RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (! $iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($source) + 1);

                if (! $iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
        $zip->close();
    }

    /**
     * @throws \Exception
     */
    public function open(string $filename, int $flags = null): bool|int
    {
        if (($result = parent::open($filename, $flags)) !== true) {
            throw new Exception(match ($result) {
                ZipArchive::ER_EXISTS => 'File already exists.',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
                ZipArchive::ER_INVAL => 'Invalid argument.',
                ZipArchive::ER_MEMORY => 'Malloc failure.',
                ZipArchive::ER_NOENT => 'No such file.',
                ZipArchive::ER_NOZIP => 'Not a zip archive.',
                ZipArchive::ER_OPEN => 'Can\'t open file.',
                ZipArchive::ER_READ => 'Read error.',
                ZipArchive::ER_SEEK => 'Seek error.',
                default => 'Unknown error(' . $result . ').'
            });
        }

        return $result;
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $files
     *
     * @throws \Exception
     */
    public static function files(array $files, string $output): void
    {
        $zip = new Zip();
        File::ensureDirectoryExists(File::dirname($output));
        $zip->open($output, ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFile($file->path(), $file->getClientOriginalName());
        }
        $zip->close();
    }

    /**
     * @return array<int, string>
     */
    public static function index(string $zipFile): array
    {
        $zipArchiveProvider = new FilesystemZipArchiveProvider(realpath($zipFile));
        $filesystem = new Filesystem(new ZipArchiveAdapter($zipArchiveProvider));
        $files = [];
        try {
            foreach ($filesystem->listContents('', true) as $fileObject) {
                if ($fileObject["type"] === "file") {
                    $files[] = $fileObject["path"];
                }
            }
        } catch (FilesystemException) {
            // ignored
        }

        return $files;
    }

    /**
     * @param  \Closure(string): string  $destination
     *
     * @throws \Exception
     */
    public static function extract(string $filename, Closure $destination): void
    {
        $zip = new Zip();
        $zip->open($filename);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (! str_ends_with($entryName, '/')) {
                $to = $destination($entryName);
                if ($to) {
                    File::ensureDirectoryExists(File::dirname($to));
                    copy("zip://" . $zip->filename . "#" . $entryName, $to);
                }
            }
        }
        $zip->close();
    }
}
