<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;

class CriminalCaseReallocateRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.admin'), 403, 'Unauthorized action.');

        return true;
    }

    public function rules(): array
    {
        return [
            'to_lawyer_id' => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'reallocation_reason' => 'required|string',
            'reallocation_date' => 'required|date',
        ];
    }
}
