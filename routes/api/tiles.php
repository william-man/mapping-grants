<?php

use App\Http\Controllers\Api\GrantTileController;
use Illuminate\Support\Facades\Route;

Route::controller(GrantTileController::class)->group(function () {
    Route::get('/grants/tiles/{z}/{x}/{y}', 'show')
    ->whereNumber(['z', 'x', 'y']);
    Route::get('/grants/tiles/{z}/{x}/{y}/summary', 'summary')
    ->whereNumber(['z', 'x', 'y']);
});