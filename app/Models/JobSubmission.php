<?php

namespace App\Models;

use App\Enums\JobState;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;

class JobSubmission extends Model
{
    protected $fillable = [
        'ip',
        'name',
        'parameters',
        'jobid',
        'status',
        'stdout',
        'uuid',
    ];

    protected $casts = [
        'parameters' => 'array',
        'status' => JobState::class,
    ];

    public function expirationTime(): \Carbon\Carbon
    {
        return $this->updated_at->addHours(config('jobsubmission.retention'));
    }

    public function expired(): bool
    {
        return $this->status->finished() &&
            now()->greaterThan($this->expirationTime());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function remainingAccessTime(): CarbonInterval
    {
        if ($this->expired()) {
            return CarbonInterval::seconds(0);
        }
        return $this->expirationTime()->diff();
    }
}
