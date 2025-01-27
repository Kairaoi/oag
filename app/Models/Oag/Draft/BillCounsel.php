<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BillCounsel extends Pivot
{
    use HasFactory;

    protected $table = 'bill_counsel';


    protected $fillable = ['bill_id', 'counsel_id', 'assigned_date', 'due_date', 'role'];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function counsel()
    {
        return $this->belongsTo(Counsel::class);
    }
}
