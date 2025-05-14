<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseParty extends Model
{
    protected $table = 'case_parties';

    protected $fillable = [
        'case_id', 'party_name', 'party_type_id', 'represented_by'
    ];

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'case_id');
    }

    public function partyType()
    {
        return $this->belongsTo(PartyType::class, 'party_type_id');
    }
}
