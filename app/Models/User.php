<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * Gate for the Filament admin panel itself — separate from any
     * permission checked once inside it. Only cm.sysadmin may log in here;
     * everyone else gets a 403 at the panel's own auth layer.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('cm.sysadmin');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function cases()
{
    return $this->belongsToMany(CivilCase::class, 'case_counsels', 'user_id', 'civil_case_id')
                ->withPivot('type', 'created_by', 'updated_by', 'created_at', 'updated_at');
}

// In User model
public function legalTasks()
{
    return $this->hasMany(LegalTask::class, 'allocated_to');
}

}
