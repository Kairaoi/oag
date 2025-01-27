<?php

namespace App\Models\Oag\Civil;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseCounsel extends Model
{
    use SoftDeletes;

    protected $table = 'case_counsels';

    protected $fillable = [
        'civil_case_id',  // Ensure this matches with the column in the migration
        'user_id',        // Foreign key for the Counsel (User)
        'type',           // Type: Plaintiff/Defendant
        'created_by',     // User who created the entry
        'updated_by',     // User who updated the entry
    ];

    // Relationship with CivilCase model
    public function civilCase()
    {
        return $this->belongsTo(CivilCase::class, 'civil_case_id');  // Ensure 'civil_case_id' is correct
    }

    // Relationship with User model (the Counsel)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');  // Ensure 'user_id' is correct
    }

    // Relationship for the 'created_by' user
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Relationship for the 'updated_by' user
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
