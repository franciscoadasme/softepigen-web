<?php

namespace App\Jobs;

use App\Contracts\JobSubmissionService;
use App\Enums\JobState;
use App\Models\JobSubmission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SubmitJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $uuid) {}

    private function failJob(string $message): void
    {
        JobSubmission::where('uuid', $this->uuid)->update([
            'status' => JobState::Failed,
            'stdout' => $message,
        ]);
        $this->fail($message);
    }

    public function handle(JobSubmissionService $service): void
    {
        $sub = JobSubmission::where('uuid', $this->uuid)->firstOrFail();
        $service->writeScript($sub);
        try {
            $jobId = $service->submit($sub);
            $sub->update(['status' => JobState::Running, 'jobid' => $jobId]);
        } catch (\Throwable $th) {
            $this->failJob($th->getMessage());
        }
    }
}
