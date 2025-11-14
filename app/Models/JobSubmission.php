<?php

namespace App\Models;

use App\Enums\JobState;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereIn(
            'status',
            array_map(fn($case) => $case->value, JobState::active()),
);
    }

    #[Scope]
    protected function expired(Builder $query): void
    {
        $query->whereNot
            ->active()
            ->where(
                'updated_at',
                '<=',
                now()->subHours((int) config('jobsubmission.retention')),
        );
    }

    public function expirationTime(): \Carbon\Carbon
    {
        return $this->updated_at->addHours(config('jobsubmission.retention'));
    }

    public function hasExpired(): bool
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
        if ($this->hasExpired()) {
            return CarbonInterval::seconds(0);
        }
        return $this->expirationTime()->diff();
    }
}
