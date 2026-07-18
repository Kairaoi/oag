<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;

class CourtCaseUpdateRequest extends FormRequest
{
    /**
     * No role/state gate exists on Court Case edit/update today.
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
            'case_id' => 'required|exists:cases,id',
            'charge_file_dated' => 'required|date',
            'high_court_case_number' => 'nullable|string|max:255',
            'magistrate_court_case_number' => 'nullable|string|max:255',
            'verdict' => 'nullable|in:guilty,not_guilty,dismissed,withdrawn,other',
            'judgment_delivered_date' => 'nullable|date',
            'court_outcome' => 'nullable|in:win,lose',
            'decision_principle_established' => 'nullable|string',
            'is_appealed' => 'nullable|boolean',
        ];
    }
}
