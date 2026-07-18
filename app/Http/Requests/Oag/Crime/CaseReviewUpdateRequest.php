<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CaseReviewUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Reallocation is an admin-level action everywhere else in the app
        // (CriminalCaseController::allocateLawyer/reallocateCase both require
        // cm.admin) — enforced here too, before any side effects, so a
        // lawyer can't use this form to move a case to themselves or
        // someone else without approval. Every other action_type is
        // unrestricted here today, matching the original controller.
        if ($this->input('action_type') === 'reallocate') {
            abort_unless($this->user()->hasRole('cm.admin'), 403, 'Only an administrator can reallocate a case.');
        }

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
            'reason_for_closure_id' => 'required_if:evidence_status,insufficient_evidence,returned_to_police|exists:reasons_for_closure,id|nullable',
            'closure_decision' => 'required_if:evidence_status,insufficient_evidence|nullable|in:nfa,nolle_prosequi',
            'offence_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offences,id',
            'category_id.*' => 'required_if:evidence_status,sufficient_evidence|nullable|exists:offence_categories,id',
            'action_type' => 'nullable|in:review,reallocate,update_court_info',
            'new_lawyer_id' => ['required_if:action_type,reallocate', 'nullable', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'reallocation_reason' => 'required_if:action_type,reallocate|nullable|string',
        ];
    }

    /**
     * The edit form has no @error/error-summary markup for new_lawyer_id or
     * reallocation_reason, so a validation failure on either previously
     * looked like the button silently did nothing — logged here (as the
     * controller used to) so a failed reallocation attempt stays visible.
     */
    protected function failedValidation(Validator $validator)
    {
        Log::warning('CaseReview update validation failed', [
            'review_id' => $this->route('CaseReview'),
            'action_type' => $this->input('action_type'),
            'errors' => $validator->errors(),
        ]);

        parent::failedValidation($validator);
    }
}
