<?php

namespace App\Models\Oag\Civil;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CivilCase extends Model
{
    use SoftDeletes;

    protected $table = 'civil_cases';

   

   // Cast attributes to the appropriate data types
   protected $casts = [
    'status_date' => 'date',
    'entered_by_sg_dsg' => 'boolean',
];
    // Fillable properties
    protected $fillable = [
        'court_category_id', 'case_type_id', 'primary_number', 'number',
        'year', 'case_name', 'case_description', 'current_status', 'status_date',
        'action_required', 'monitoring_status', 'entered_by_sg_dsg', 'created_by', 'updated_by'
    ];
    
    
    

    // Define relationships
    public function courtCategory()
    {
        return $this->belongsTo(CourtCategory::class);
    }

    public function caseType()
    {
        return $this->belongsTo(CaseType::class);
    }

   
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function counsels()
    {
        return $this->belongsToMany(\App\Models\User::class, 'case_counsels', 'civil_case_id', 'user_id')
                    ->withPivot('type', 'created_by', 'updated_by', 'created_at', 'updated_at');
    }
    

    
}
