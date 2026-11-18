<?php

namespace App\Support;

class Tile {
    public static function bounds(int $coordZ, int $coordX, int $coordY): array
    {
       $total = 2 ** $coordZ;
       $west = $coordX / $total * 360 - 180;
       $east = ($coordX + 1) / $total * 360 - 180;
       $north = rad2deg(atan((sinh(M_PI * (1 - 2 * $coordY / $total)))));
       $south = rad2deg(atan((sinh(M_PI * (1 - 2 * ($coordY + 1) / $total)))));
       
       return [
        'west' => $west,
        'east' => $east,
        'north' => $north,
        'south' => $south
        ];
    }
}