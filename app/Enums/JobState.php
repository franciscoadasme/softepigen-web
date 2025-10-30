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

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
