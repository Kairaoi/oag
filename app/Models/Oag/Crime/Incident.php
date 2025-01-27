<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incidents';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'case_id',
        'lawyer_id',
        'island_id',
        'date_of_incident_start',
        'date_of_incident_end',
        'place_of_incident',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_of_incident_start' => 'date',
        'date_of_incident_end' => 'date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    public static $rules = [
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'date_of_incident_start' => 'nullable|date',
        'date_of_incident_end' => 'nullable|date',
        'place_of_incident' => 'required|string|max:255',
        'created_by' => 'required|exists:users,id',
        'updated_by' => 'nullable|exists:users,id',
    ];

    /**
     * Relationships
     */

    // Relationship with the CriminalCase model
    public function criminalCase()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    // Relationship with the User model for lawyer
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    // Relationship with the Island model
    public function island()
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    // Relationship with the User model for created by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with the User model for updated by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
