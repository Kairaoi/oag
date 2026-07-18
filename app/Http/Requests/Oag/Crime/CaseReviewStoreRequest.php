<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;

class CaseReviewStoreRequest extends FormRequest
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
        abort_unless($case->status === 'accepted', 403, 'This case must be accepted before it can be reviewed.');
        $this->assertCaseIsActionable($case);

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'evidence_status' => 'required|in:pending_review,sufficient_evidence,insufficient_evidence,returned_to_police',
            'review_date' => 'required|date',
            'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|nullable|exists:reasons_for_closure,id',
            'closure_decision' => 'required_if:evidence_status,insufficient_evidence|nullable|in:nfa,nolle_prosequi',
            'offence_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offences,id',
            'category_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offence_categories,id',
            'offence_particulars' => 'required_if:evidence_status,sufficient_evidence|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason_for_closure_id.required_if' => 'Please select a reason for closing the case.',
        ];
    }
}
