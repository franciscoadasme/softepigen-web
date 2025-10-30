<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class CompressFile implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $path) {}

    public function handle(): void
    {
        if (Storage::missing($this->path)) {
            $this->fail("File to compress not found: {$this->path}");
        }

        $abspath = Storage::path($this->path);
        $result = Process::run('gzip -f ' . escapeshellarg($abspath));

        if ($result->failed()) {
            $this->fail(
                "Something went wrong with the compression: {$this->path}\n{$result->errorOutput()}",
            );
        }

        if (Storage::missing($this->path . '.gz')) {
            $this->fail(
                "Something went wrong with the compression: {$this->path}",
            );
        }
    }
}
