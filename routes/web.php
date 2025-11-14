<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::controller(JobController::class)->group(function () {
    Route::get('/', 'create')->name('jobs.create');
    Route::middleware(['throttle:job-submissions'])->group(function () {
        Route::post('/submit', 'store')->name('jobs.store');
    });
    Route::middleware('auth.ip')->group(function () {
        Route::get('/jobs/{job_submission}', 'show')->name('jobs.show');
        Route::get('/jobs/{job_submission}/download/{filetype}', 'download')
            ->name('jobs.download')
            ->whereIn('filetype', ['bed', 'csv']);
    });
});
