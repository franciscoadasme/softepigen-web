<tr>
    <td class="py-3 pl-5">
        <x-link href="{{ route('jobs.show', $job->uuid) }}">
            {{ $job->name }}
        </x-link>
        <small class="text-slate-400">({{ $job->uuid }})</small>
    </td>
    <td class="px-3 py-3">
        {{ $job->updated_at->diffForHumans() }}
    </td>
    <td class="flex items-center gap-2 py-3 pr-5 pl-3">
        @switch($job->status)
            @case(\App\Enums\JobState::Completed)
                <x-icons.check class="size-4 fill-green-600" />

                @break
            @case(\App\Enums\JobState::Failed)
                <x-icons.error class="size-4 fill-red-600" />

                @break
            @default
                <x-icons.refresh class="size-4 animate-spin fill-slate-700" />
        @endswitch
        {{ Str::title($job->status->value) }}
    </td>
</tr>
