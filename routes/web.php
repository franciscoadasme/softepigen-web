<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::controller(JobController::class)->group(function () {
    Route::get('/', 'create')->name('jobs.create');
    Route::post('/submit', 'store')->name('jobs.store');
    Route::get('/jobs/{job_submission}', 'show')->name('jobs.show');
});
