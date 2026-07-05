<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserHasRole implements Rule
{
    public function __construct(private string $role)
    {
    }

    public function passes($attribute, $value): bool
    {
        $user = User::find($value);

        return $user && $user->hasRole($this->role);
    }

    public function message(): string
    {
        return "The selected user must have the {$this->role} role.";
    }
}
