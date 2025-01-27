<?php

namespace App\Models\Oag\Crime;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accused extends Model
{
    use SoftDeletes;

    protected $table = 'accused';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'case_id',
        'lawyer_id',
        'island_id',
        'first_name',
        'last_name',
        'accused_particulars',
        'gender',
        'date_of_birth',
        'created_by',
        'updated_by',
    ];

    public static $rules = [
        'case_id' => 'required|exists:cases,id',
        'lawyer_id' => 'required|exists:users,id',  // Update to 'lawyer_id'
        'island_id' => 'required|exists:islands,id',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'accused_particulars' => 'required|string',
        'gender' => 'required|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'created_by' => 'required|exists:users,id',
        'updated_by' => 'nullable|exists:users,id',
    ];

    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    public function lawyer()  // Update to 'lawyer' instead of 'user'
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function island()
    {
        return $this->belongsTo(Island::class, 'island_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function offences()
    {
        return $this->belongsToMany(Offence::class, 'accused_offence', 'accused_id', 'offence_id');
    }

    public function getGenderAttribute($value)
    {
        return ucfirst($value);
    }
}
