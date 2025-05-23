<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Oag\Crime\CriminalCase; // Assumes case model is in this namespace

class CourtOfAppeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'court_of_appeals';
    protected $primaryKey = 'id';

    protected $fillable = [
        'case_id',
        'appeal_case_number',
        'appeal_filing_date',
        'filing_date_source',
        'judgment_delivered_date',
        'court_outcome',
        'decision_principle_established',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationship to the main case.
     */
    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    /**
     * User who created the record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
