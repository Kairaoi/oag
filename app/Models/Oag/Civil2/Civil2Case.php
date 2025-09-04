<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Oag\Civil\CourtCategory;

class Civil2Case extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'civil2_cases';

   protected $fillable = [
    'case_name',
    'case_file_no',
    'court_case_no',
    'date_received',
    'date_opened',
    'court_type_id',
    'date_closed',
    'cause_of_action_id',
    'responsible_counsel_id',
   
    // 'case_pending_status_id',
    'case_origin_type_id',
    // 'case_description',
    'created_by',
    'updated_by',

    // Optional: If you're passing these through request and want them mass assignable
    'plaintiff_counsels',
    'defendant_counsels',
];

    protected $casts = [
        'date_received' => 'date',
        'date_opened' => 'date',
        'date_closed' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function counsel()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsible_counsel_id');
    }

    public function causeOfAction()
    {
        return $this->belongsTo(CauseOfAction::class, 'cause_of_action_id');
    }

    public function statuses()
{
    return $this->hasMany(CaseStatus::class, 'case_id');
}

    public function pendingStatus()
    {
        return $this->belongsTo(CasePendingStatus::class, 'case_pending_status_id');
    }

    public function originType()
    {
        return $this->belongsTo(CaseOriginType::class, 'case_origin_type_id');
    }

    public function courtType()
    {
        return $this->belongsTo(CaseType::class, 'court_type_id');
    }

    public function parties()
    {
        return $this->hasMany(CaseParty::class, 'case_id');
    }

    public function activities()
    {
        return $this->hasMany(CaseActivity::class, 'case_id');
    }

    public function closures()
    {
        return $this->hasOne(CaseClosure::class, 'case_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(CaseStatusHistory::class, 'case_id');
    }

    public function quarterlyReportCases()
    {
        return $this->hasMany(QuarterlyReportCase::class, 'case_id');
    }

    public function caseCounsels()
{
    return $this->hasMany(CasseCounsel::class, 'civil2_case_id');
}


public function plaintiffCounsels()
{
    return $this->caseCounsels()->where('role', 'plaintiff');
}

public function defendantCounsels()
{
    return $this->caseCounsels()->where('role', 'defendant');
}

}
