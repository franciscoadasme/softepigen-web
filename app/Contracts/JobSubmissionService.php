<?php

namespace App\Contracts;

use App\Enums\JobState;
use App\Models\JobSubmission;

interface JobSubmissionService
{
    public function status(JobSubmission $job): JobState;
    public function submit(JobSubmission $job): int;
    public function writeScript(JobSubmission $job): string;
}
