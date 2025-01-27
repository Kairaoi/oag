<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulationHistory extends Model
{
    use HasFactory;

    protected $table = 'regulation_history';

    protected $fillable = ['regulation_id', 'action', 'changed_by', 'details'];

    // Relationships
    public function regulation()
    {
        return $this->belongsTo(Regulation::class);
    }
}
