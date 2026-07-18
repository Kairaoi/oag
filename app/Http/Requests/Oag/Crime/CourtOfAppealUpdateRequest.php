<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;

class CourtOfAppealUpdateRequest extends FormRequest
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
            'appeal_case_number' => 'nullable|string',
            'appeal_filing_date' => 'required|date',
            'filing_date_source' => 'required|string',
            'judgment_delivered_date' => 'nullable|date',
            'court_outcome' => 'nullable|in:win,lose',
            'decision_principle_established' => 'nullable|string',
        ];
    }
}
