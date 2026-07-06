<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportGroup extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
