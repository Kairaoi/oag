<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    protected $fillable = [
        'report_group_id', 'name', 'description', 'query'
    ];

    public function group()
    {
        return $this->belongsTo(ReportGroup::class, 'report_group_id'); // Ensure correct foreign key
    }
}