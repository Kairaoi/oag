<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PartyType extends Model
{
    protected $table = 'party_types';

    protected $fillable = [
        'name', 'description'
    ];
}
