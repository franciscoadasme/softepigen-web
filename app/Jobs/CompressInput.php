<?php

namespace App\Jobs;

use App\Enums\JobState;
use App\Helpers\StorageHelper;
use App\Models\JobSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompressInput implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $uuid) {}

    public function handle(): void
    {
        $path = "jobs/{$this->uuid}/input.fasta";
        try {
            StorageHelper::compress($path);
            JobSubmission::where('uuid', $this->uuid)->update([
                'status' => JobState::Ready,
            ]);
        } catch (\RuntimeException $e) {
            $this->failJob($e->getMessage());
        }
    }

    private function failJob(string $message): void
    {
        JobSubmission::where('uuid', $this->uuid)->update([
            'status' => JobState::Failed,
            'stdout' => $message,
        ]);
        $this->fail($message);
    }
}
