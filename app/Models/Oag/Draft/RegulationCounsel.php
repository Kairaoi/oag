<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RegulationCounsel extends Pivot
{
    use HasFactory;

    protected $table = 'regulation_counsel';

    protected $fillable = ['regulation_id', 'counsel_id', 'assigned_date', 'due_date', 'role'];

    // Relationships
    public function regulation()
    {
        return $this->belongsTo(Regulation::class);
    }

    public function counsel()
    {
        return $this->belongsTo(Counsel::class);
    }
}
