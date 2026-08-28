<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grant;

class FundingOrganisationController extends Controller
{
    public function index()
    {
        return response()->json(Grant::uniqueFundingOrganizations());
    }
}