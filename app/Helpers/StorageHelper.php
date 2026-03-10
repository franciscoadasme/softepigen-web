<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Process;

class StorageHelper
{
    public static function compress(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File to compress not found: {$path}");
        }

        $result = Process::run('gzip -f ' . escapeshellarg($path));
        if ($result->failed()) {
            throw new \RuntimeException(
                "Something went wrong with the compression: {$path}\n{$result->errorOutput()}",
            );
        }

        if (!file_exists($path . '.gz')) {
            throw new \RuntimeException(
                "Something went wrong with the compression: {$path}",
            );
        }
    }
}
