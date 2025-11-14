<?php

namespace App\Enums;

enum JobState: string
{
    case Completed = 'completed';
    case Failed = 'failed';
    case Queued = 'queued';
    case Ready = 'ready';
    case Running = 'running';
    case Started = 'started';

    public static function active(): array
    {
        return array_filter(self::cases(), fn($case) => !$case->finished());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function completed(): bool
    {
        return $this == self::Completed;
    }

    public function failed(): bool
    {
        return $this == self::Failed;
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Completed, self::Failed => true,
            default => false,
        };
    }

    public function queued(): bool
    {
        return $this == self::Queued;
    }

    public function ready(): bool
    {
        return $this == self::Ready;
    }

    public function running(): bool
    {
        return $this == self::Running;
    }

    public function started(): bool
    {
        return $this == self::Started;
    }
}
