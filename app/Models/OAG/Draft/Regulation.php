<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory;

    protected $table = 'regulations';

    protected $fillable = [
        'name', 'receipt_date', 'ministry_id', 'status', 'priority',
        'comments', 'target_completion_date', 'actual_completion_date',
        'version', 'requires_cabinet_approval'
    ];

    // Relationships
    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function regulationCounsels()
    {
        return $this->hasMany(RegulationCounsel::class);
    }

    public function regulationHistories()
    {
        return $this->hasMany(RegulationHistory::class);
    }
}
