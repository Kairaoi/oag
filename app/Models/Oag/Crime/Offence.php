<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offences'; // Assuming your table is named 'offences'
    protected $primaryKey = 'id'; // Changed to 'id'
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'offence_name',
        'offence_category_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'offence_category_id' => 'int',
    ];

    protected $dates = ['deleted_at'];

    public static $rules = [
        'offence_name' => 'required|string|max:255',
        'offence_category_id' => 'required|integer|exists:offence_categories,id',
    ];

    public function offenceCategory()
    {
        return $this->belongsTo(OffenceCategory::class, 'offence_category_id');
    }

    public function accused()
    {
        return $this->belongsToMany(Accused::class, 'accused_offence', 'offence_id', 'accused_id');
    }
}
