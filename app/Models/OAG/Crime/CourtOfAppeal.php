<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\OAG\Crime\CriminalCase; // Assumes case model is in this namespace

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
     * case_name isn't a column on this table — it only ever arrives as a
     * joined 'cases.case_name' select alias — so this accessor fires
     * whenever it's read (DataTables listing, show page, etc.) and reverses
     * the caption when the defendant, not the court, filed the appeal.
     */
    public function getCaseNameAttribute($value)
    {
        return \App\Support\CaseCaption::forAppeal($value, $this->filing_date_source);
    }

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
