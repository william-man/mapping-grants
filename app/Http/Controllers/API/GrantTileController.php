<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Support\Tile;

class GrantTileController extends Controller
{
    public function show(int $z, int $x, int $y)
    {
        $bounds = Tile::bounds($z, $x, $y);
        $sql = "SELECT 'organisation' AS type,
                   r.organisationID AS id,
                   r.name,
                   r.latitude,
                   r.longitude,
                   1 AS recipients,
                   COUNT(*) AS grants
            FROM recipient_organisations r
            JOIN grants g ON g.recipientOrganisation = r.organisationID
            WHERE r.latitude  BETWEEN ? AND ?
              AND r.longitude BETWEEN ? AND ?
            GROUP BY r.organisationID, r.name, r.latitude, r.longitude

            UNION ALL

            SELECT 'individuals' AS type,
                   NULL AS id,
                   NULL AS name,
                   i.latitude,
                   i.longitude,
                   COUNT(DISTINCT i.individualID) AS recipients,
                   COUNT(*) AS grants
            FROM recipient_individuals i
            JOIN grants g ON g.recipientIndividual = i.individualID
            WHERE i.latitude  BETWEEN ? AND ?
              AND i.longitude BETWEEN ? AND ?
            GROUP BY i.latitude, i.longitude";

        $grants = DB::select($sql, [
            $bounds['south'],
            $bounds['north'],
            $bounds['west'],
            $bounds['east'],
            $bounds['south'],
            $bounds['north'],
            $bounds['west'],
            $bounds['east'],
        ]);

        return response()->json($grants);
    }

    public function summary(int $z, int $x, int $y)
    {
        $bounds = Tile::bounds($z, $x, $y);
        $sql = "
        SELECT 'organisations' AS type,
        COUNT(DISTINCT r.organisationID) AS recipients,
        COUNT(*) AS grants
        FROM recipient_organisations r
        JOIN grants g ON g.recipientOrganisation = r.organisationID
        WHERE r.latitude  BETWEEN ? AND ?
        AND r.longitude BETWEEN ? AND ?

        UNION ALL

        SELECT 'individuals' AS type,
            COUNT(DISTINCT i.individualID) AS recipients,
            COUNT(*) AS grants
        FROM recipient_individuals i
        JOIN grants g ON g.recipientIndividual = i.individualID
        WHERE i.latitude  BETWEEN ? AND ?
        AND i.longitude BETWEEN ? AND ?
        ";

        $grants = DB::select($sql, [
            $bounds['south'],
            $bounds['north'],
            $bounds['west'],
            $bounds['east'],
            $bounds['south'],
            $bounds['north'],
            $bounds['west'],
            $bounds['east'],
        ]);

        return response()->json($grants);
    }
}