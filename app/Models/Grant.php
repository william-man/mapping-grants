<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grant extends Model
{
    protected $table = 'grants';

    protected $casts = [
        'fundingOrganization' => 'array',
        'recipientOrganization' => 'array',
        'recipientIndividual' => 'array',
    ];

    protected static function selectByColumn(string $column, int $paginate = 20)
    {
        return self::select($column)->distinct()->whereNotNull($column)->paginate($paginate);
    }

    public static function uniquePostcodes($perPage = 20)
    {
        return self::selectByColumn('recipientPostcode', $perPage);
    }

    public static function uniqueFundingOrganizations($perPage = 20)
    {
        return self::query()
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(fundingOrganization, '$[0].id')) as funder_id")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(fundingOrganization, '$[0].name')) as funder_name")
            ->distinct()
            ->paginate($perPage);
    }
}