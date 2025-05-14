<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseClosure extends Model
{
    protected $table = 'case_closures';

    protected $fillable = [
        'case_id', 'memo_date', 'sg_clearance', 'sg_clearance_date',
        'ag_endorsement', 'ag_endorsement_date', 'file_archived', 'file_archived_date',
        'closed_by', 'closure_notes'
    ];

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'case_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
