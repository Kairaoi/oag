<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseReallocation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'case_reallocations';

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
        'case_id',
        'from_lawyer_id',
        'to_lawyer_id',
        'reallocation_reason',
        'reallocation_date',
        'created_by',
        'updated_by', // Add this
    ];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reallocation_date' => 'date',
    ];

    /**
     * Relationships
     */

    // Relation with the CriminalCase model
    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    // Relation with the User model (From Lawyer)
    public function fromLawyer()
    {
        return $this->belongsTo(User::class, 'from_lawyer_id');
    }

    // Relation with the User model (To Lawyer)
    public function toLawyer()
    {
        return $this->belongsTo(User::class, 'to_lawyer_id');
    }

    // Relation with the User model for created by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
