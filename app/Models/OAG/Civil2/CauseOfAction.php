<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CauseOfAction extends Model
{
    protected $table = 'causes_of_action';

    protected $fillable = [
        'name', 'description'
    ];
}
