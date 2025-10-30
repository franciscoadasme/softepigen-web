<?php

namespace App\Http\Controllers;

use App\Jobs\CompressInput;
use App\Helpers\FASTAHelper;
use App\Models\JobSubmission;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fasta' => [
                'required',
                'file',
                'mimes:txt,fasta,fa',
                'max:512000', # 500MB
                function (string $attribute, mixed $value, Closure $fail) {
                    if (!FASTAHelper::isValid($value)) {
                        $fail(
                            'The contents of the file is not valid nucleic FASTA.',
                        );
                    }
                },
            ],
            'amplicon_size_min' => 'required|integer|min:1',
            'amplicon_size_max' =>
                'required|integer|min:1|gt:amplicon_size_min',
            'primer_size_min' => 'required|integer|min:1',
            'primer_size_max' => 'required|integer|min:1|gt:primer_size_min',
            'cpg_min' => 'required|integer|min:1',
            'cpg_max' => 'required|integer|min:1|gt:cpg_min',
            'astringent' => 'boolean',
        ]);

        $uuid = Str::uuid()->toString();
        $file = $request->file('fasta');
        $path = $file->storeAs("jobs/$uuid", 'input.fasta');

        $job = JobSubmission::create([
            'uuid' => $uuid,
            'ip' => $request->ip(),
            'parameters' => [
                'amplicon_range' => [
                    (int) $validated['amplicon_size_min'],
                    (int) $validated['amplicon_size_max'],
                ],
                'primer_range' => [
                    (int) $validated['primer_size_min'],
                    (int) $validated['primer_size_max'],
                ],
                'cpg_range' => [
                    (int) $validated['cpg_min'],
                    (int) $validated['cpg_max'],
                ],
                'astringent' => ((bool) $validated['astringent']) ?? false,
            ],
        ]);

        CompressInput::dispatch($uuid)->withoutDelay();

        return response($path, 200);
    }
}
