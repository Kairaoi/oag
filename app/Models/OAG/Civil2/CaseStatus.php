<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseStatus extends Model
{
    protected $table = 'case_statuses';

   protected $fillable = [
    'case_id',
    'status_date',
    'current_status',
    'action_required',
    'monitoring_status',
    'created_by',
    'updated_by',
];
public function case()
{
    return $this->belongsTo(Civil2Case::class, 'case_id');
}
}
