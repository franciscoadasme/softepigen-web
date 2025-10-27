<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fasta' => 'required|file|mimes:txt,fasta,fa|max:512000', # 500MB
            'amplicon_size_min' => 'required|integer|min:1',
            'amplicon_size_max' =>
                'required|integer|min:1|gt:amplicon_size_min',
            'primer_size_min' => 'required|integer|min:1',
            'primer_size_max' => 'required|integer|min:1|gt:primer_size_min',
            'cpg_min' => 'required|integer|min:1',
            'cpg_max' => 'required|integer|min:1|gt:cpg_min',
            'astringent' => 'boolean',
        ]);
        return response('OK', 200);
    }
}
