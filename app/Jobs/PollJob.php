<?php

namespace App\Jobs;

use App\Enums\JobState;
use App\Models\JobSubmission;
use App\Contracts\JobSubmissionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class PollJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $uuid) {}

    public function handle(JobSubmissionService $service): void
    {
        $sub = JobSubmission::where('uuid', $this->uuid)->firstOrFail();
        $status = $service->status($sub);
        $stderr = null;

        if ($status === JobState::Failed) {
            $stderr = Storage::get("jobs/{$sub->uuid}/stderr");
        }

        if ($status !== $sub->status) {
            $sub->update(['status' => $status, 'stdout' => $stderr]);
        }

        if (!in_array($status, [JobState::Completed, JobState::Failed])) {
            $this->requeue();
        }
    }

    protected function requeue(): void
    {
        self::dispatch($this->uuid)->delay(
            now()->addSeconds(config('jobsubmission.poll_interval')),
        );
    }
}
