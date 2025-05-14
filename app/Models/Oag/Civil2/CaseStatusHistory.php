<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseStatusHistory extends Model
{
    protected $table = 'case_status_history';

    protected $fillable = [
        'case_id', 'case_status_id', 'case_pending_status_id', 'updated_by', 'notes'
    ];

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'case_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
