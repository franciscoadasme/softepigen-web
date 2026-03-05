<?php

namespace App\Services;

use App\Contracts\JobSubmissionService;
use App\Enums\JobState;
use App\Models\JobSubmission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SlurmProxyService implements JobSubmissionService
{
    public function status(JobSubmission $job): JobState
    {
        $url = rtrim(config('jobsubmission.proxy'), '/') . '/status';
        $response = Http::timeout(15)
            ->withHeaders($this->authHeaders())
            ->get($url, ['job' => $job->jobid]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Could not retrieve job status from Slurm proxy: ' .
                    ($response->json('error') ?? $response->body()),
            );
        }

        $state = $response->json('state', 'FAILED');
        switch ($state) {
            case 'RUNNING':
            case 'COMPLETING':
                return JobState::Running;
            case 'PENDING':
            case 'REQUEUED':
                return JobState::Queued;
            case 'COMPLETED':
                return JobState::Completed;
            default:
                if (Storage::size("jobs/{$job->uuid}/stderr") > 0) {
                    return JobState::Failed;
                } else {
                    return JobState::Completed;
                }
        }
    }

    public function submit(JobSubmission $job): int
    {
        $url = rtrim(config('jobsubmission.proxy'), '/') . '/submit';

        $response = Http::timeout(30)
            ->withHeaders($this->authHeaders())
            ->post($url, [
                'workdir' => "jobs/{$job->uuid}",
                'script' => 'softepigen.slurm',
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Slurm submission failed: ' .
                    ($response->json('error') ?? $response->body()),
            );
        }

        $jobId = $response->json('job_id');
        if ($jobId === null) {
            throw new RuntimeException('Slurm proxy did not return job id');
        }

        return (int) $jobId;
    }

    public function writeScript(JobSubmission $job): string
    {
        return app()
            ->make(\App\Services\SlurmService::class)
            ->writeScript($job);
    }

    private function authHeaders(): array
    {
        $token = config('jobsubmission.token');
        if ($token !== null) {
            return ['Authorization' => 'Bearer ' . $token];
        }
        return [];
    }
}
