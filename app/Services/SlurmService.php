<?php

namespace App\Services;

use App\Contracts\JobSubmissionService;
use App\Enums\JobState;
use App\Models\JobSubmission;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class SlurmService implements JobSubmissionService
{
    public function status(JobSubmission $job): JobState
    {
        // -j specifies the job ID
        // -n removes the header
        // -o state specifies we only want the state column
        // -X prevents showing every individual step (just the main job)
        $result = Process::run("sacct -j {$job->jobid} -n -o state -X");
        $output = trim($result->output());
        if ($result->successful() && !empty($output)) {
            return match ($output) {
                'RUNNING', 'COMPLETING' => JobState::Running,
                'PENDING', 'REQUEUED' => JobState::Queued,
                'COMPLETED' => JobState::Completed,
                // FAILED, CANCELLED, TIMEOUT, NODE_FAIL, PREEMPTED
                default => JobState::Failed,
            };
        }

        $result = Process::run("squeue -j $job->jobid -h -o %T");
        switch (trim($result->output())) {
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
        $workdir = Storage::path("jobs/{$job->uuid}");
        $result = Process::path($workdir)->run(
            'sbatch --parsable softepigen.slurm',
        );
        if ($result->failed()) {
            throw new RuntimeException(
                'Slurm submission failed: ' . $result->errorOutput(),
            );
        }
        return (int) trim($result->output());
    }

    public function writeScript(JobSubmission $job): string
    {
        $workdir = Storage::path("jobs/{$job->uuid}");
        $params = $job->parameters;
        $astringency = $params['astringent'] ? 1 : 0;

        $partition = config('jobsubmission.slurm_partition');

        $contents = <<<BASH
        #!/usr/bin/env bash
        #SBATCH -J softepigen_{$job->uuid}
        #SBATCH -p {$partition}
        #SBATCH -N 1
        #SBATCH -c 1
        #SBATCH --mem=2G
        #SBATCH -o stdout
        #SBATCH -e stderr

        set -euo pipefail

        cd $workdir
        softepigen \
            --amplicon={$params['amplicon_range'][0]},{$params['amplicon_range'][1]} \
            --primer={$params['primer_range'][0]},{$params['primer_range'][1]} \
            --cpg={$params['cpg_range'][0]},{$params['cpg_range'][1]} \
            --astringency=$astringency \
            --output=output \
            input.fasta.gz

        rm -f input.fasta.gz
        BASH;

        return Storage::put("jobs/{$job->uuid}/softepigen.slurm", $contents);
    }
}
