<?php

namespace App\Providers;

use App\Contracts\JobSubmissionService;
use App\Services\BashService;
use App\Services\SlurmService;
use Illuminate\Contracts\Foundation\Application;
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
        //
    }
}
