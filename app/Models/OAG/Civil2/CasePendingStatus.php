<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CasePendingStatus extends Model
{
    protected $table = 'case_pending_statuses';

    protected $fillable = [
        'name', 'description'
    ];
}
