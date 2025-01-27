<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OffenceCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offence_categories'; // Assuming your table is named 'offence_categories'

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
        'category_name',
        'created_by',
        'updated_by',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    public static $rules = [
        'category_name' => 'required|string|max:255',
    ];

    public function offences()
{
    return $this->hasMany(Offence::class, 'offence_category_id', 'offence_category_id');
}

}