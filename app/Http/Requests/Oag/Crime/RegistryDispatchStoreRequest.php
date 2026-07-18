<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\AgReviewRepository;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use App\Repositories\Oag\Crime\RegistryDispatchRepository;
use Illuminate\Foundation\Http\FormRequest;

class RegistryDispatchStoreRequest extends FormRequest
{
    use AuthorizesCriminalCase;

    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.registrar'), 403);

        $case = app(CriminalCaseRepository::class)->getById($this->input('case_id'));

        // A missing case is handled by the controller's own abort_if(!$case, 404).
        if (!$case) {
            return true;
        }

        $this->assertCaseIsActionable($case);

        // A case can only be dispatched once the AG has approved it, and only
        // once — mirrors RegistryDispatchController::assertReadyForDispatch(),
        // duplicated here since that method is controller-private and this
        // same rule also gates the GET create() form.
        $latestAgReview = app(AgReviewRepository::class)->getLatestForCase($case->id);
        abort_unless(
            $latestAgReview && $latestAgReview->ag_decision === 'approved',
            403,
            'This case must be approved by the AG before it can be dispatched.'
        );

        abort_if(
            app(RegistryDispatchRepository::class)->hasDispatch($case->id),
            403,
            'This case has already been dispatched.'
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
            'date_dispatched' => 'required|date',
            'dispatched_to' => 'required|string|max:255',
        ];
    }
}
