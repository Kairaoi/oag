<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Contracts\Validation\Validator;
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
            'offences' => 'required_if:evidence_status,sufficient_evidence|nullable|array',
            'offences.*.offence_name' => 'required_with:offences.*.category_id|nullable|string|max:255',
            'offences.*.category_id' => 'required_with:offences.*.offence_name|nullable|exists:offence_categories,id',
            'offences.*.domestic_violence' => 'nullable|boolean',
            'offence_particulars' => 'required_if:evidence_status,sufficient_evidence|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason_for_closure_id.required_if' => 'Please select a reason for closing the case.',
        ];
    }

    /**
     * "offences" being present and non-empty (per rules() above) isn't
     * enough on its own — a row can exist with both offence_name and
     * category_id left blank, which previously let a review save as
     * "Sufficient Evidence" with nothing actually charged (see the case
     * this closed off: a review saved with offence_particulars filled in
     * but zero rows ever synced to case_offence). This requires at least
     * one row to be genuinely filled in.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('evidence_status') !== 'sufficient_evidence') {
                return;
            }

            $rows = collect($this->input('offences', []))->filter(function ($row) {
                return trim($row['offence_name'] ?? '') !== '' && !empty($row['category_id']);
            });

            if ($rows->isEmpty()) {
                $validator->errors()->add('offences', 'Please charge at least one offence, with a category selected, before submitting.');
            }
        });
    }
}
