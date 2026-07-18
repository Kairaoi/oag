<?php

namespace App\Models\OAG\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class AgReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ag_reviews';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'case_id',
        'submitted_by',
        'submitted_at',
        'submission_notes',
        'ag_decision',
        'decision_date',
        'ag_comments',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submitted_at' => 'date',
        'decision_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
