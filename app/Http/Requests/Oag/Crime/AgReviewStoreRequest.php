<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\AgReviewRepository;
use App\Repositories\Oag\Crime\CaseReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;

class AgReviewStoreRequest extends FormRequest
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

        // A case is only ready for AG submission once its Case Review found
        // sufficient evidence — mirrors AgReviewController::assertReadyForAgReview(),
        // duplicated here since that method is controller-private and this
        // same rule also gates the GET create() form.
        $review = app(CaseReviewRepository::class)->getReviewsByCaseId($case->id)->first();
        abort_unless(
            $review && $review->evidence_status === 'sufficient_evidence',
            403,
            'This case must have a Case Review with sufficient evidence before it can be submitted to the AG.'
        );

        abort_if(
            app(AgReviewRepository::class)->hasActiveSubmission($case->id),
            403,
            'This case already has an AG review pending or approved.'
        );

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'submitted_at' => 'required|date',
            'submission_notes' => 'nullable|string',
        ];
    }
}
