<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Oag\Crime\CriminalCase;
use App\Models\Oag\Crime\CourtCase;
use App\Models\Lookup\Island; // Assumed Island model
use App\Models\Oag\Crime\Lawyer; // Assumed Lawyer model

class AppealDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'appeal_details';
    protected $primaryKey = 'id';

   // In AppealDetail model
protected $fillable = [
    'case_id', // Make sure 'case_id' is in the fillable array
    'appeal_case_number',
    'appeal_filing_date',
    'court_outcome',
    'judgment_delivered_date',
    'verdict',
    'decision_principle_established',
    'created_by',
    'updated_by',
];


    /**
     * Relationship to the original criminal case.
     */
    public function originalCase()
    {
        return $this->belongsTo(CriminalCase::class, 'original_case_id');
    }

    /**
     * Relationship to the court of appeal.
     */
    public function courtOfAppeal()
    {
        return $this->belongsTo(CourtCase::class, 'court_of_appeal_id');
    }

    /**
     * Relationship to the lawyer.
     */
    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    /**
     * Relationship to the island.
     */
    public function island()
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    /**
     * User who created the appeal.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the appeal.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
