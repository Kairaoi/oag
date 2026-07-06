<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseOriginType extends Model
{
    protected $table = 'case_origin_types';

    protected $fillable = [
        'name', 'description'
    ];
}
