<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobSubmissionRequest;
use App\Jobs\CompressInputJob;
use App\Jobs\PollJob;
use App\Jobs\SubmitJob;
use App\Models\JobSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function create()
    {
        return view('jobs.create', [
            'jobs' => JobSubmission::where('ip', request()->ip())
                ->latest()
                ->get(),
        ]);
    }

    public function download(JobSubmission $jobSubmission, string $filetype)
    {
        $path = "jobs/{$jobSubmission->uuid}/output-out.{$filetype}.gz";
        return response()->streamDownload(
            function () use ($path) {
                $gz = gzopen(Storage::path($path), 'rb');
                while (!gzeof($gz)) {
                    echo gzread($gz, 8192);
                }
                gzclose($gz);
            },
            basename($jobSubmission->name, '.fasta') . "-out.{$filetype}",
            ['Content-Type' => 'text/plain'],
        );
    }

    public function show(JobSubmission $jobSubmission)
    {
        return view('jobs.show', ['job' => $jobSubmission]);
    }

    public function store(StoreJobSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $uuid = Str::uuid()->toString();
        $file = $request->file('fasta');
        $path = $file->storeAs("jobs/$uuid", 'input.fasta');

        $job = JobSubmission::create([
            'uuid' => $uuid,
            'name' => $file->getClientOriginalName(),
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
                'astringent' => (bool) ($validated['astringent'] ?? false),
            ],
        ]);

        Bus::chain([
            new CompressInputJob($uuid),
            new SubmitJob($uuid),
            new PollJob($uuid),
        ])->dispatch();

        return redirect()->route('jobs.show', ['job_submission' => $uuid]);
    }
}
