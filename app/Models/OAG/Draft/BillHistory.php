<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillHistory extends Model
{
    use HasFactory;
    
    protected $table = 'bill_history';


    protected $fillable = ['bill_id', 'action', 'changed_by', 'details'];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
