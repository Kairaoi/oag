<?php

namespace App\Models\Oag\Civil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class CourtAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'court_attendances';

    protected $fillable = [
        'civil_case_id',
        'opposing_counsel_name',
        'hearing_date',
        'hearing_type',
        'hearing_time',
        'case_status',
        'status_notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationships
     */

    // Relation to CivilCase model
    public function civilCase()
    {
        return $this->belongsTo(CivilCase::class);
    }

    // Relation to User model for created_by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation to User model for updated_by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function counsels()
    {
        return $this->belongsToMany(User::class, 'case_counsels', 'civil_case_id', 'user_id')
                    ->withPivot('type');  // Include the 'type' field from the pivot table
    }
}
