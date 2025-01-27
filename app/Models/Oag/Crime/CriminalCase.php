<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriminalCase extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases'; // Assuming your table is named 'cases'

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
     * The "type" of the auto-incrementing ID.
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
        'case_file_number',
        'date_file_received',
        'case_name',
        'date_of_allocation',
        'date_file_closed',
        'reason_for_closure_id',
        'lawyer_id',
        'island_id',
        'created_by',
        'updated_by',
    ];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_file_received' => 'date',
        'date_file_closed' => 'date',
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
        'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number',
        'date_file_received'    => 'required|date',
        'case_name'             => 'required|string|max:255',
        'date_of_allocation'    => 'nullable|date',
        'date_file_closed'      => 'nullable|date',
        'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
        'lawyer_id'             => 'required|exists:users,id',
        'island_id'             => 'required|exists:islands,id',
    ];

    /**
     * Relationships
     */

    // Relation with the User model (Lawyer)
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    // Relation with the Island model
    public function island()
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    // Relation with the User model for created by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation with the User model for updated by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relation with the closure reason
    public function closureReason()
    {
        return $this->belongsTo(ClosureReason::class, 'reason_for_closure_id');
    }

    // Relation with the Accused model
    public function accused()
    {
        return $this->hasMany(Accused::class, 'case_id');
    }
}
