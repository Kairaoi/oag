<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';


    protected $fillable = [
        'name',
        'receipt_date',
        'ministry_id',
        'status',
        'priority',
        'task',
        'progress_status',
        'comments',
        'target_completion_date',
        'actual_completion_date',
        'version',
    ];

    // Relationships
    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function billCounsels()
    {
        return $this->hasMany(BillCounsel::class);
    }

    public function billHistories()
    {
        return $this->hasMany(BillHistory::class);
    }
}
