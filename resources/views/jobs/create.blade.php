<x-layout>
    <x-card class="mb-10">
        <x-slot:title>Upload FASTA file</x-slot>
        <x-slot:footer>
            A job will be submitted to the queue. You'll be redirected to the
            job status page.
        </x-slot>

        @error('limit')
            <p
                class="-mt-3 mb-3 rounded-r-md border-l-3 border-l-red-500 bg-red-50 px-5 py-1 text-sm font-semibold text-red-500"
            >
                {{ $message }}
            </p>
        @enderror

        <form
            method="POST"
            action="{{ route('jobs.store') }}"
            enctype="multipart/form-data"
            class="space-y-4"
            x-data="{ submitting: false }"
            x-on:submit="submitting = true"
        >
            @csrf
            <div
                class="rounded-lg border-2 border-dashed border-slate-300 p-6 text-center transition-colors duration-200 hover:border-slate-400"
            >
                <input
                    type="file"
                    name="fasta"
                    accept=".fasta,.fa,.fas,.txt"
                    class="block w-full text-center transition-colors duration-200 file:mr-4 file:rounded-full file:border-0 file:bg-primary file:px-4 file:py-2 file:font-semibold file:text-white"
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
                        value="{{ old('amplicon_size_min') ?? 100 }}"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="amplicon_size_max"
                        value="{{ old('amplicon_size_max') ?? 150 }}"
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
                        value="{{ old('primer_size_min') ?? 15 }}"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="primer_size_max"
                        value="{{ old('primer_size_max') ?? 25 }}"
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
                        value="{{ old('cpg_min') ?? 3 }}"
                        min="1"
                        class="w-16 rounded border border-slate-300 px-2 py-1"
                        required
                    />
                    &ndash;
                    <input
                        type="number"
                        name="cpg_max"
                        value="{{ old('cpg_max') ?? 40 }}"
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
                        @if(old('astringent')) checked @endif
                    />
                    <label for="astringent">
                        Use astringency for complexity analysis
                    </label>
                </div>
            </div>
            @if (config('services.turnstile.key'))
                <div class="flex justify-center">
                    <div
                        class="cf-turnstile"
                        data-sitekey="{{ config('services.turnstile.key') }}"
                        data-theme="light"
                    ></div>
                </div>
                @error('cf-turnstile-response')
                    <x-form.error>{{ $message }}</x-form.error>
                @enderror
            @endif

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 font-semibold text-white shadow-md transition-colors duration-200 hover:bg-[oklch(from_var(--color-primary)_calc(l+0.1)_c_h)] not-disabled:active:translate-y-0.5 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:opacity-70"
                x-bind:disabled="submitting"
            >
                <svg
                    x-show="submitting"
                    x-cloak
                    class="h-5 w-5 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                    ></path>
                </svg>
                <span
                    x-text="submitting ? 'Submitting...' : 'Analyze'"
                ></span>
            </button>
        </form>
    </x-card>

    @if ($jobs->isNotEmpty())
        <x-jobs.list :$jobs class="mb-10" />
    @endif
</x-layout>
