<?php

namespace App\Providers;

use App\Contracts\JobSubmissionService;
use App\Services\BashService;
use App\Services\SlurmService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JobSubmissionService::class, function (
            Application $app,
        ) {
            $driver = config('jobsubmission.driver');
            return match ($driver) {
                'bash' => $app->make(BashService::class),
                'slurm' => $app->make(SlurmService::class),
                default => throw new \InvalidArgumentException(
                    "Unknown job submission driver [$driver]",
                ),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('job-submissions', function (Request $request) {
            return Limit::perMinute(config('jobsubmission.limit'))->by(
                $request->ip(),
            );
        });
    }
}
