<?php

namespace App\Http\Middleware;

use App\Models\JobSubmission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveJobsLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $active = JobSubmission::where('ip', $request->ip())->active()->count();
        $cap = config('jobsubmission.max_jobs');
        if ($active >= $cap) {
            return back()->withErrors([
                'limit' => "You've reached the active job limit ($cap). Wait for a job to finish.",
            ]);
        }
        return $next($request);
    }
}
