<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Postcode;

class PostcodeController extends Controller
{
    public function search(string $postcode)
    {
        $normalised = $this->normalise($postcode);
    }

    public function normalise(string $postcode)
    {
        $postcode = preg_replace('/\s+/', '', $postcode);
        return strtolower($postcode);
    }
}