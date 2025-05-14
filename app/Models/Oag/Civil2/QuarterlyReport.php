<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class QuarterlyReport extends Model
{
    protected $table = 'quarterly_reports';

    protected $fillable = [
        'counsel_id', 'year', 'quarter', 'submitted_date', 'is_submitted', 'notes'
    ];

    public function counsel()
    {
        return $this->belongsTo(User::class);
    }

    public function cases()
    {
        return $this->hasMany(QuarterlyReportCase::class);
    }
}
