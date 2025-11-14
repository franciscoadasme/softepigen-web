<x-layout>
    <x-card>
        <x-slot:title>{{ $job->name }}</x-slot>
        <x-slot:header>
            <x-jobs.status-indicator :$job class="my-10 text-center" />
        </x-slot>

        <x-jobs.parameters :$job />

        @if ($job->status == \App\Enums\JobState::Completed)
            <p
                class="small-caps mt-1 mb-2 text-sm font-semibold text-slate-400"
            >
                Download output
            </p>
            <div class="inline-flex gap-2">
                @foreach (['bed', 'csv'] as $filetype)
                    <a
                        href="{{
                            route('jobs.download', [
                                'job_submission' => $job->uuid,
                                'filetype' => $filetype,
                            ])
                        }}"
                        class="inline-flex size-20 flex-col items-center justify-center rounded-lg bg-slate-200 px-6 py-3 hover:bg-slate-300 active:bg-slate-400"
                    >
                        <x-dynamic-component
                            :component="'icons.file-' . $filetype "
                            class="size-6 fill-blue-600"
                        />
                        .{{ $filetype }}
                    </a>
                @endforeach
            </div>
            <x-slot:footer>
                @if ($job->expired())
                    Job has expired and will be
                    <strong>deleted soon</strong>
                    &ZeroWidthSpace;.
                @else
                    Job will be available only for
                    <strong>
                        {{ $job->remainingAccessTime()->forHumans(['parts' => 1]) }}
                    </strong>
                    and then deleted.
                @endif
            </x-slot>
        @endif
    </x-card>

    @unless ($job->status->finished())
        <x-slot:meta>
            <meta
                http-equiv="refresh"
                content="{{ config('jobsubmission.poll_interval', 30) }}"
            />
        </x-slot>

        <p class="mt-4 text-center text-sm text-slate-400">
            <x-icons.refresh class="mr-1 inline size-3 animate-spin" />
            This page will refresh automatically every
            {{ config('jobsubmission.poll_interval', 30) }} seconds.
        </p>
    @endunless

    <div class="mt-5 text-center">
        <a
            href="{{ route('jobs.create') }}"
            class="text-slate-500 hover:text-blue-500 hover:underline active:text-blue-600"
        >
            &larr; Back to home
        </a>
    </div>
</x-layout>
