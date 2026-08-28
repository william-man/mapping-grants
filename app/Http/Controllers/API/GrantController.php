<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grant;

class GrantController extends Controller
{
    public function index()
    {
        $grants = Grant::paginate(20);
        return response()->json($grants);
    }

    public function getID(string $id)
    {
        $grant = Grant::find($id);
        if ($grant) {
            return response()->json($grant);
        } else {
            return response()->json(['message' => 'Grant not found'], 404);
        }
    }
}