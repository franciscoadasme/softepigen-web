<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/', [JobController::class, 'create'])->name('jobs.create');
Route::post('/submit', [JobController::class, 'store'])->name('jobs.store');
