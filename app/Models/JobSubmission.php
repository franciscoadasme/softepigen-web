<?php

namespace App\Models;

use App\Enums\JobState;
use Illuminate\Database\Eloquent\Model;

class JobSubmission extends Model
{
    protected $fillable = [
        'ip',
        'parameters',
        'slurm_id',
        'status',
        'stdout',
        'uuid',
    ];

    protected $casts = [
        'parameters' => 'array',
        'status' => JobState::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
