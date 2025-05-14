<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class QuarterlyReportCase extends Model
{
    protected $table = 'quarterly_report_cases';

    protected $fillable = [
        'quarterly_report_id', 'case_id', 'other_counsel', 'current_status', 'required_work'
    ];

    public function quarterlyReport()
    {
        return $this->belongsTo(QuarterlyReport::class);
    }

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'case_id');
    }
}
