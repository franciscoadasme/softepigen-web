<x-layout>
    <div
        class="mx-auto w-[52rem] rounded-xl border border-slate-200 bg-white p-5"
    >
        <h2 class="mb-6 text-2xl font-semibold">Upload FASTA file</h2>
        <form
            method="POST"
            action="{{ route('jobs.store') }}"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            <div
                class="rounded-lg border-2 border-dashed border-slate-300 p-6 text-center transition-colors duration-200 hover:border-slate-400"
            >
                <input
                    type="file"
                    name="fasta"
                    accept=".fasta,.fa,.fas,.txt"
                    class="block w-full text-center transition-colors duration-200 file:mr-4 file:rounded-full file:border-0 file:bg-blue-500 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-600"
                    required
                />
                <p class="mt-2 text-sm text-slate-500">
                    Supported formats: .fasta, .fa, .fas, .txt
                </p>
            </div>
            @error('fasta')
                <x-form.error>{{ $message }}</x-form.error>
            @enderror

            <div
                class="grid grid-cols-3 gap-x-5 gap-y-2 text-sm text-slate-700"
            >
                <div>
                    <label for="amplicon_size_min">Amplicon size:</label>
                    <input
                        type="number"
                        name="amplicon_size_min"
                        value="100"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="amplicon_size_max"
                        value="150"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    @error('amplicon_size_min')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror

                    @error('amplicon_size_max')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror
                </div>
                <div>
                    <label for="primer_size_min">Primer size:</label>
                    <input
                        type="number"
                        name="primer_size_min"
                        value="15"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="primer_size_max"
                        value="25"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    @error('primer_size_min')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror

                    @error('primer_size_max')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror
                </div>
                <div>
                    <label for="cpg_min">Number of CpG:</label>
                    <input
                        type="number"
                        name="cpg_min"
                        value="3"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="cpg_max"
                        value="40"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    @error('cpg_min')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror

                    @error('cpg_max')
                        <x-form.error>{{ $message }}</x-form.error>
                    @enderror
                </div>
                <div class="col-span-3">
                    <input
                        type="checkbox"
                        id="astringent"
                        name="astringent"
                        value="1"
                    />
                    <label for="astringent">
                        Use astringency for complexity analysis
                    </label>
                </div>
            </div>
            <button
                type="submit"
                class="w-full rounded-lg bg-blue-500 px-6 py-3 font-semibold text-white shadow-md transition-colors duration-200 hover:bg-blue-600 disabled:cursor-not-allowed disabled:bg-blue-500 disabled:opacity-70"
            >
                Analyze
            </button>
            <p class="text-center text-sm text-slate-400">
                A job will be submitted to the queue. You'll be redirected to
                the job status page.
            </p>
        </form>
    </div>
</x-layout>
