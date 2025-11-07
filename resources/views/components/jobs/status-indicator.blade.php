@props([
    'job',
])

<div {{ $attributes->class(['animate-pulse' => ! $job->status->finished()]) }}>
    @switch($job->status)
        @case(\App\Enums\JobState::Started)
            <x-icons.file-zipper class="inline-block size-12 fill-blue-600" />
            <p class="mt-2 font-semibold text-slate-500">
                Compressing input...
            </p>

            @break
        @case(\App\Enums\JobState::Queued)
            <x-icons.queue class="inline-block size-12 fill-yellow-600" />
            <p class="mt-2 font-semibold text-slate-500">
                Waiting to start calculation...
            </p>

            @break
        @case(\App\Enums\JobState::Running)
            <x-icons.duration class="inline-block size-12 fill-cyan-500" />
            <p class="mt-2 font-semibold text-slate-500">
                Running calculation...
            </p>

            @break
        @case(\App\Enums\JobState::Completed)
            <x-icons.check class="inline-block size-12 fill-green-600" />
            <p class="mt-2 font-semibold text-slate-500">Job Completed</p>

            @break
        @case(\App\Enums\JobState::Failed)
            <x-icons.error class="inline-block size-12 fill-red-600" />
            <p class="mt-2 font-semibold text-slate-500">Job Failed</p>

            @break
        @default
    @endswitch
</div>
