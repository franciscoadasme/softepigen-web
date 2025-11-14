<x-card {{ $attributes }}>
    <x-slot:title>Active Jobs</x-slot>
    <div class="-mx-5">
        <table class="min-w-full">
            <thead class="border-b border-slate-200">
                <tr>
                    <td
                        class="small-caps pb-3 pl-5 text-sm font-semibold text-slate-400"
                    >
                        Name
                    </td>
                    <td
                        class="small-caps px-3 pb-3 text-sm font-semibold text-slate-400"
                    >
                        Last update
                    </td>
                    <td
                        class="small-caps pr-5 pb-3 pl-3 text-sm font-semibold text-slate-400"
                    >
                        Status
                    </td>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @each('components.jobs.list-item', $jobs, 'job')
            </tbody>
        </table>
    </div>
    <x-slot:footer>
        Jobs are available for
        <strong>{{ config('jobsubmission.retention') }} hours</strong>
        after completion and then deleted.
    </x-slot>
</x-card>
