<?php

namespace App\Models\Oag\Civil2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Oag\Civil2\CivilCase;
use App\Models\Oag\Civil2\ExternalCounsel;

class CasseCounsel extends Model
{
    protected $table = 'casse_counsels';

    protected $fillable = [
        'civil2_case_id',
        'counsel_id',
        'counsel_type',
        'role',
    ];

    public function case()
    {
        return $this->belongsTo(CivilCase::class, 'civil2_case_id');
    }

    // Polymorphic relation: internal or external counsel
    public function counsel()
    {
        return $this->morphTo(null, 'counsel_type', 'counsel_id');
    }

    public function civil2Case()
{
    return $this->belongsTo(Civil2Case::class);
}

}
