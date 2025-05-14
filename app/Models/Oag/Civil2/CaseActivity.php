<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseActivity extends Model
{
    protected $table = 'case_activities';

    protected $fillable = [
        'case_id', 'activity_type', 'activity_date', 'description', 'performed_by', 'document_reference'
    ];

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'case_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
