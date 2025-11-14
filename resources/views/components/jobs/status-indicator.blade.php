@props([
    'job',
])

<div {{ $attributes->class(['animate-pulse' => ! $job->status->finished()]) }}>
    @if ($job->status->started())
        <x-icons.file-zipper class="inline-block size-12 fill-blue-600" />
        <p class="mt-2 font-semibold text-slate-500">Compressing input...</p>
    @elseif ($job->status->queued())
        <x-icons.queue class="inline-block size-12 fill-yellow-600" />
        <p class="mt-2 font-semibold text-slate-500">
            Waiting to start calculation...
        </p>
    @elseif ($job->status->running())
        <x-icons.duration class="inline-block size-12 fill-cyan-500" />
        <p class="mt-2 font-semibold text-slate-500">Running calculation...</p>
    @elseif ($job->status->completed())
        <x-icons.check class="inline-block size-12 fill-green-600" />
        <p class="mt-2 font-semibold text-slate-500">Job Completed</p>
    @elseif ($job->status->failed())
        <x-icons.error class="inline-block size-12 fill-red-600" />
        <p class="mt-2 font-semibold text-slate-500">Job Failed</p>
    @endif
</div>
