<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Victim extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'victims';

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
        'first_name',
        'last_name',
        'victim_particulars',
        'gender',
        'date_of_birth',
        'age_group', // Add this line
        'created_by',
        'updated_by'
    ];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    public static $rules = [
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',
        'island_id' => 'required|exists:islands,id',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'victim_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'created_by' => 'required|exists:users,id',
        'updated_by' => 'nullable|exists:users,id',
    ];

    /**
     * Get the case that owns the accused.
     */
    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    // Relation with the User model (Lawyer)
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
    /**
     * Get the island that owns the accused.
     */
    public function island()
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    /**
     * Get the user who created the accused record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the accused record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the accused's gender as a readable string.
     *
     * @return string
     */
    public function getGenderAttribute($value)
    {
        return ucfirst($value);
    }
}
