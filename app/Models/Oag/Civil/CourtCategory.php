<?php

namespace App\Models\Oag\Civil;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtCategory extends Model
{
    use SoftDeletes;

    protected $table = 'court_categories'; // Updated table name to match the migration

    protected $fillable = [
        'name', 
        'code', // Add 'code' to fillable attributes
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
        return $this->hasMany(CivilCase::class, 'case_category_id');
    }
}
