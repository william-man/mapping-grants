<?php

use App\Http\Controllers\Api\RecipientPostcodeController;
use Illuminate\Support\Facades\Route;

Route::controller(RecipientPostcodeController::class)->group(function () {
    Route::get('/recipient-postcodes', 'index');
});