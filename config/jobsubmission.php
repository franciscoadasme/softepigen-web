<?php

return [
    'driver' => env('JOB_SUBMISSION_DRIVER', 'bash'),
    'limit' => env('JOB_SUBMISSION_LIMIT', 10), # per minute
    'max_jobs' => env('JOB_SUBMISSION_MAX', 5),
    'poll_interval' => env('JOB_SUBMISSION_POLL_INTERVAL', 30), # seconds
    'retention' => env('JOB_SUBMISSION_RETENTION', 24), # hours
];
