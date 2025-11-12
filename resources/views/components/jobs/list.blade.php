<div
    class="mx-auto my-10 w-[52rem] rounded-xl border border-slate-200 bg-white p-5"
>
    <h2 class="mb-6 text-2xl font-semibold">Active Jobs</h2>
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
            <tfoot>
                <tr>
                    <td
                        class="mt-4 pt-3 text-center text-sm text-slate-400"
                        colspan="3"
                    >
                        Jobs are available for
                        <strong>24 hours</strong>
                        after completion and then deleted.
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
