<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard/admin/home');

/**
 * ADMIN ROUTES
 */
Route::get('/dashboard/{any}', [ApplicationController::class, 'index'])->where('any', '(.*)');
