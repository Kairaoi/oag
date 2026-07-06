<?php

namespace App\Models\Oag\Civil;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseType extends Model
{
    use SoftDeletes;

    protected $table = 'case_types';

    protected $fillable = [
        'name', 
        'created_by', 
        'updated_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function cases()
    {
        return $this->hasMany(CivilCase::class, 'case_type_id');
    }
}
