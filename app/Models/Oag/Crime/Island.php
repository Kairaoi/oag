<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Island extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'islands';

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
        'island_name',
       
        'created_by',
        'updated_by',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    public static $rules = [
        'island_name' => 'required|string|max:255',
        'created_by' => 'required|exists:users,id',
        'updated_by' => 'nullable|exists:users,id',
    ];

    /**
     * Get the user who created the island record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the island record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the island's name as a readable string.
     *
     * @return string
     */
    public function getIslandNameAttribute($value)
    {
        return ucfirst($value);
    }
}
