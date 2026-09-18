<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grant extends Model
{
    protected $table = 'grants';

    public function fundingOrganisation()
    {
        return $this->belongsTo(FundingOrganisation::class, 'fundingOrganisation', 'organisationID');
    }

    public function recipientOrganisation()
    {
        return $this->belongsTo(RecipientOrganisation::class, 'recipientOrganisation', 'organisationID');
    }

    public function recipientIndividual()
    {
        return $this->belongsTo(RecipientIndividual::class, 'recipientIndividual', 'individualID');
    }
}