<?php

namespace App\Services;

use App\Contracts\JobSubmissionService;
use App\Enums\JobState;
use App\Models\JobSubmission;
use Illuminate\Support\Facades\Storage;

class BashService implements JobSubmissionService
{
    public function status(JobSubmission $job): JobState
    {
        if (posix_kill($job->jobid, 0)) {
            return JobState::Running;
        } elseif (Storage::size("jobs/{$job->uuid}/stderr") > 0) {
            return JobState::Failed;
        } else {
            return JobState::Completed;
        }
    }

    public function submit(JobSubmission $job): int
    {
        $workdir = escapeshellarg(Storage::path("jobs/{$job->uuid}"));
        $cmd = "cd $workdir && nohup bash run.sh >stdout 2>stderr & echo $!";
        return (int) trim(shell_exec($cmd));
    }

    public function writeScript(JobSubmission $job): string
    {
        $workdir = Storage::path("jobs/{$job->uuid}");
        $params = $job->parameters;
        $astringency = $params['astringent'] ? 1 : 0;

        $contents = <<<BASH
        #!/usr/bin/env bash

        cd $workdir
        softepigen \
            --amplicon={$params['amplicon_range'][0]},{$params['amplicon_range'][1]} \
            --primer={$params['primer_range'][0]},{$params['primer_range'][1]} \
            --cpg={$params['cpg_range'][0]},{$params['cpg_range'][1]} \
            --astringency=$astringency \
            --output=output \
            input.fasta.gz || echo "Something went wrong with softepigen" >&2

        rm -f input.fasta.gz
        BASH;

        return Storage::put("jobs/{$job->uuid}/run.sh", $contents);
    }
}
