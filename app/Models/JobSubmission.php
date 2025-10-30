<?php

namespace App\Models;

use App\Enums\JobState;
use Illuminate\Database\Eloquent\Model;

class JobSubmission extends Model
{
    protected $fillable = ['uuid', 'ip', 'parameters'];

    protected $casts = [
        'parameters' => 'array',
        'status' => JobState::class,
    ];

    public function isFinished(): bool
    {
        return $this->status === JobState::Completed ||
            $this->status === JobState::Failed;
    }
}
