<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseStatus extends Model
{
    protected $table = 'case_statuses';

    protected $fillable = [
        'name', 'description'
    ];
}
