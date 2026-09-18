<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grant;
use Illuminate\Support\Facades\DB;

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

    public function getMappable()
    {
        // $grants = Grant::with(['recipientOrganisation', 'recipientIndividual'])
        //     ->whereHas('recipientOrganisation', function ($query) {
        //         $query->whereNotNull('latitude')->whereNotNull('longitude');
        //     })
        //     ->orWhereHas('recipientIndividual', function ($query) {
        //         $query->whereNotNull('latitude')->whereNotNull('longitude');
        //     })
        //     ->limit(500,000)
        //     ->get();
        // 0.001 s

        //         SELECT * From grants
        // INNER JOIN recipient_organisations
        // ON recipient_organisations.organisationID = grants.recipientOrganisation
        // WHERE recipient_organisations.latitude IS NOT NULL
        // LIMIT 100

        $grants = DB::table('grants')
        ->join('recipient_organisations', 'organisationID', '=', 'grants.recipientOrganisation')
        ->where('recipient_organisations.latitude', '!=', null)
        ->limit(100)
        ->get();
        // 1,000 rows (0.093 s)
        return response()->json($grants);
    }
}