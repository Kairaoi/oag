<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;

class CourtCaseStoreRequest extends FormRequest
{
    use AuthorizesCriminalCase;

    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.user'), 403);

        $case = app(CriminalCaseRepository::class)->getById($this->input('case_id'));

        // A missing case is handled by the controller's own abort_if(!$case, 404).
        if (!$case) {
            return true;
        }

        $this->assertCanActOnCase($case, $this->user());
        $this->assertCaseIsActionable($case);
        $this->assertCaseIsDispatched($case);

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
