<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppealDetailUpdateRequest extends FormRequest
{
    /**
     * Closing a pre-existing gap — edit/update had no role check at all
     * (create/store already required cm.user).
     */
    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.user'), 403);

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'high_court_case_number' => 'nullable|string|max:255',
            'magistrate_court_case_number' => 'nullable|string|max:255',
            'appeal_case_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('appeal_details', 'appeal_case_number')->ignore($this->route('appeal'))->whereNull('deleted_at'),
            ],
            'appeal_filing_date' => 'nullable|date',
            'filing_date_source' => 'nullable|in:court,defendant',
            'appeal_status' => 'required|in:pending,appealed,dismissed,withdrawn',
            'appeal_grounds' => 'nullable|string',
            'appeal_decision' => 'nullable|string',
            'appeal_decision_date' => 'nullable|date',
        ];
    }
}
