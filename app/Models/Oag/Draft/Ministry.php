<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    use HasFactory;

    protected $table = 'ministries';

    protected $fillable = ['name', 'code', 'is_active'];

    // Relationships
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function regulations()
    {
        return $this->hasMany(Regulation::class);
    }
}
