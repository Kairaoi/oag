<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Oag\Crime\CriminalCase;

class CourtHearing extends Model
{
    use SoftDeletes;

    protected $table = 'court_hearings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'case_id',
        'hearing_date',
        'hearing_type',
        'hearing_notes',
        'is_completed',
        'has_verdict',
        'verdict',
        'verdict_details',
        'verdict_date',
        'sentencing_details',
        'created_by',
        'updated_by',
    ];

    public static $rules = [
        'case_id' => 'required|exists:cases,id',
        'hearing_date' => 'required|date',
        'hearing_type' => 'required|string',
        'hearing_notes' => 'nullable|string',
        'is_completed' => 'boolean',
        'has_verdict' => 'boolean',
        'verdict' => 'nullable|in:guilty,not_guilty,dismissed,withdrawn,other',
        'verdict_details' => 'nullable|string',
        'verdict_date' => 'nullable|date',
        'sentencing_details' => 'nullable|string',
        'created_by' => 'required|exists:users,id',
        'updated_by' => 'nullable|exists:users,id',
    ];

    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
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
