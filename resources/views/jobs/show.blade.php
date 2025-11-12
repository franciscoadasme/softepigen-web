<x-layout>
    <div
        class="mx-auto w-[52rem] rounded-xl border border-slate-200 bg-white p-5"
    >
        <x-jobs.status-indicator :$job class="my-10 text-center" />
        <h2 class="mb-5 text-2xl font-semibold">Job {{ $job->uuid }}</h2>
        <x-jobs.parameters :$job />
    </div>

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
            class="text-slate-500 hover:text-blue-500 active:text-blue-600"
        >
            &larr; Back to home
        </a>
    </div>
</x-layout>
