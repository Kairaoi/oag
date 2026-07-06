<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;

class ExternalCounsel extends Model
{
    protected $table = 'external_counsels';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    // Reverse relation (optional)
    public function caseCounsels()
    {
        return $this->morphMany(CaseCounsel::class, 'counsel');
    }
}
