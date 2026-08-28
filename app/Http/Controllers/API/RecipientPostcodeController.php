<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grant;

class RecipientPostcodeController extends Controller
{
    public function index()
    {
        $recipientPostcodes = Grant::uniquePostcodes(); 
        return response()->json($recipientPostcodes);
    }
}