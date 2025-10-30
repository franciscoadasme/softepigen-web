<?php

namespace App\Jobs;

use App\Enums\JobState;
use App\Models\JobSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class CompressInput implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $uuid) {}

    public function handle(): void
    {
        $path = "jobs/{$this->uuid}/input.fasta";

        if (Storage::missing($path)) {
            $this->failJob("File to compress not found: {$path}");
        }

        $abspath = Storage::path($path);
        $result = Process::run('gzip -f ' . escapeshellarg($abspath));

        if ($result->failed()) {
            $this->failJob(
                "Something went wrong with the compression: {$path}\n{$result->errorOutput()}",
            );
        }

        if (Storage::missing($path . '.gz')) {
            $this->failJob(
                "Something went wrong with the compression: {$path}",
            );
        }

        JobSubmission::where('uuid', $this->uuid)->update([
            'status' => JobState::Ready,
        ]);
    }

    private function failJob(string $message): void
    {
        JobSubmission::where('uuid', $this->uuid)->update([
            'status' => JobState::Failed,
        ]);
        $this->fail($message);
    }
}
