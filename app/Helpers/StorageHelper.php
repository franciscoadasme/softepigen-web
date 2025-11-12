<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    public static function compress(string $path): void
    {
        if (Storage::missing($path)) {
            throw new \RuntimeException("File to compress not found: {$path}");
        }

        $abspath = Storage::path($path);
        $result = Process::run('gzip -f ' . escapeshellarg($abspath));

        if ($result->failed()) {
            throw new \RuntimeException(
                "Something went wrong with the compression: {$path}\n{$result->errorOutput()}",
            );
        }

        if (Storage::missing($path . '.gz')) {
            throw new \RuntimeException(
                "Something went wrong with the compression: {$path}",
            );
        }
    }
}
