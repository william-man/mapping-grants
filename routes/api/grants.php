<?php

use App\Http\Controllers\Api\GrantController;
use Illuminate\Support\Facades\Route;

Route::controller(GrantController::class)->group(function () {
    Route::get('/grants', 'index');
    Route::get('/grants/{id}', 'getID');
});