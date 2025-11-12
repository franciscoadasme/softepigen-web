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
        switch ($status) {
            case JobState::Completed:
                CompressOutputJob::dispatch($this->uuid)->withoutDelay();
                break;
            case JobState::Failed:
                $stderr = Storage::get("jobs/{$sub->uuid}/stderr");
                $sub->update(['status' => $status, 'stdout' => $stderr]);
                break;
            default:
                if ($status !== $sub->status) {
                    $sub->update(['status' => $status]);
                }
                $this->requeue();
                break;
        }
    }

    protected function requeue(): void
    {
        self::dispatch($this->uuid)->delay(
            now()->addSeconds(config('jobsubmission.poll_interval')),
        );
    }
}
