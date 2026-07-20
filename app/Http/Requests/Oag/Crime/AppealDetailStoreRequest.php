<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppealDetailStoreRequest extends FormRequest
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
        $this->assertAgHasApprovedCase($case);

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'high_court_case_number' => 'nullable|string|max:255',
            'magistrate_court_case_number' => 'nullable|string|max:255',
            'appeal_case_number' => [
                'required',
                'string',
                Rule::unique('appeal_details', 'appeal_case_number')->whereNull('deleted_at'),
            ],
            'filing_date_type' => 'required|in:court,defendant',
            'filing_date_value' => 'required|date',
            'court_outcome' => 'required|string',
            'judgment_delivered_date' => 'nullable|date',
            'appeal_status' => 'required|in:pending,appealed,dismissed,withdrawn',
            'decision_principle_established' => 'nullable|string',
        ];
    }
}
