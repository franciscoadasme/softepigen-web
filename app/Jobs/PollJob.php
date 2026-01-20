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
        try {
            $status = $service->status($sub);
        } catch (\Throwable $th) {
            $this->failJob($th->getMessage());
            return;
        }
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
