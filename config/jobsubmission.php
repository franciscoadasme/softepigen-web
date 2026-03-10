<?php

return [
    'driver' => env('JOB_SUBMISSION_DRIVER', 'bash'),
    'limit' => (int) env('JOB_SUBMISSION_LIMIT', 10), # per minute
    'max_jobs' => (int) env('JOB_SUBMISSION_MAX', 5),
    'poll_interval' => (int) env('JOB_SUBMISSION_POLL_INTERVAL', 30), # seconds
    'retention' => (int) env('JOB_SUBMISSION_RETENTION', 24), # hours
    'bin' => env('JOB_SUBMISSION_BIN', 'softepigen'),
    'modules' => env('JOB_SUBMISSION_MODULES'),
    'slurm_partition' => env('JOB_SUBMISSION_QUEUE', 'normal'),
    'proxy' => env(
        'JOB_SUBMISSION_PROXY',
        'http://host.containers.internal:9393',
    ),
    'token' => env('JOB_SUBMISSION_TOKEN'),
    'spool' => env('JOB_SUBMISSION_SPOOL', '/data/softepigen/'),
];
