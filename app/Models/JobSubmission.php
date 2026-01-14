<?php

namespace App\Models;

use App\Enums\JobState;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Storage;

class JobSubmission extends Model
{
    use Prunable;

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
        $query
            ->finished()
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

    #[Scope]
    protected function finished(Builder $query): void
    {
        $query->whereNot(function (Builder $query) {
            $query->active();
        });
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

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::query()->expired();
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        Storage::deleteDirectory("jobs/{$this->uuid}");
    }

    public function remainingAccessTime(): CarbonInterval
    {
        if ($this->hasExpired()) {
            return CarbonInterval::seconds(0);
        }
        return $this->expirationTime()->diff();
    }
}
