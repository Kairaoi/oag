<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;

class CriminalCaseStoreRequest extends FormRequest
{
    /**
     * Case creation has no role gate today — any authenticated user may
     * register a new case file.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_file_number'      => 'required|string|max:255|unique:cases,case_file_number',
            'date_file_received'    => 'required|date',
            'case_name'             => 'required|string|max:255',
            'date_of_incident'      => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
            'lawyer_id'             => ['nullable', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'island_id'             => 'required|exists:islands,id',
            'court_case_number'     => 'nullable|string|max:255',
        ];
    }
}
