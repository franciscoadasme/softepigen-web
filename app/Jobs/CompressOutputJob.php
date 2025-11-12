<?php

namespace App\Jobs;

use App\Enums\JobState;
use App\Helpers\StorageHelper;
use App\Models\JobSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompressOutputJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $uuid) {}

    public function handle(): void
    {
        $prefix = "jobs/{$this->uuid}/output-out";
        foreach (['.bed', '.csv'] as $ext) {
            try {
                StorageHelper::compress($prefix . $ext);
            } catch (\RuntimeException $e) {
                $this->failJob($e->getMessage());
                return;
            }
        }

        JobSubmission::where('uuid', $this->uuid)->update([
            'status' => JobState::Completed,
        ]);
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
