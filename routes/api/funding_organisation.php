<?php

use App\Http\Controllers\Api\FundingOrganisationController;
use Illuminate\Support\Facades\Route;

Route::controller(FundingOrganisationController::class)->group(function () {
    Route::get('/funding-organisations', 'index');
});