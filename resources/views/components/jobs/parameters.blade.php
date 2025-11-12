@props([
    'job',
])

<dl class="grid grid-cols-4 gap-x-5 gap-y-2">
    <div class="col-span-2">
        <dd class="small-caps text-sm font-semibold text-slate-400">ID</dd>
        <dt class="mb-4">{{ $job->uuid }}</dt>
    </div>
    <div class="col-span-2">
        <dd class="small-caps text-sm font-semibold text-slate-400">
            @if ($job->status->finished())
                Finish date
            @else
                    Last update
            @endif
        </dd>
        <dt class="mb-4">
            {{ $job->updated_at->diffForHumans() }}
            <span class="text-slate-400">({{ $job->updated_at }})</span>
        </dt>
    </div>
    <div>
        <dd class="small-caps text-sm font-semibold text-slate-400">
            Amplicon size
        </dd>
        <dt class="mb-4">
            {{ $job->parameters['amplicon_range'][0] }}
            &ndash;
            {{ $job->parameters['amplicon_range'][1] }}
            bp
        </dt>
    </div>
    <div>
        <dd class="small-caps text-sm font-semibold text-slate-400">
            Primer size
        </dd>
        <dt class="mb-4">
            {{ $job->parameters['primer_range'][0] }}
            &ndash;
            {{ $job->parameters['primer_range'][1] }}
            bp
        </dt>
    </div>
    <div>
        <dd class="small-caps text-sm font-semibold text-slate-400">
            Number of CpGs
        </dd>
        <dt class="mb-4">
            {{ $job->parameters['cpg_range'][0] }}
            &ndash;
            {{ $job->parameters['cpg_range'][1] }}
            bp
        </dt>
    </div>
    <div>
        <dd class="small-caps text-sm font-semibold text-slate-400">
            Use astringency?
        </dd>
        <dt class="mb-4">
            {{ $job->parameters['astringent'] ? 'Yes' : 'No' }}
        </dt>
    </div>
</dl>
