<?php

namespace App\Models\OAG\Draft;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counsel extends Model
{
    use HasFactory;

    protected $table = 'counsels';

    protected $fillable = ['name', 'position', 'is_active', 'max_assignments'];

    // Relationships
    public function billCounsels()
    {
        return $this->hasMany(BillCounsel::class);
    }

    public function regulationCounsels()
    {
        return $this->hasMany(RegulationCounsel::class);
    }
}
