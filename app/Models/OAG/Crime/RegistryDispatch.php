<?php

namespace App\Models\OAG\Crime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class RegistryDispatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'registry_dispatches';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'case_id',
        'dispatched_by',
        'date_dispatched',
        'dispatched_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_dispatched' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(CriminalCase::class, 'case_id');
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
