<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CaseReview extends Model
{
    use SoftDeletes;
    
    protected $table = 'case_reviews';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'case_id',
        'evidence_status',
        'review_date',
        'created_by',
        'updated_by',
        'action_type',        // New field to track review action
        'new_lawyer_id',      // Stores the new assigned lawyer (if reallocated)
        'reallocation_reason',// Stores reason for reassignment
       'offence_particulars',
        'date_file_closed',   // Add this field
        'reason_for_closure_id' // Add this field
    ];
    /**
     * Default relationships to prevent N+1 queries.
     */
    protected $with = ['case', 'createdBy', 'updatedBy', 'newLawyer']; // Removed 'lawyer' from here

    /**
     * Relationships
     */
    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    public function newLawyer()
    {
        return $this->belongsTo(User::class, 'new_lawyer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessors & Mutators
     */
    public function getReviewNotesAttribute($value)
    {
        return ucfirst($value ?? ''); // Handles null values safely
    }
}
