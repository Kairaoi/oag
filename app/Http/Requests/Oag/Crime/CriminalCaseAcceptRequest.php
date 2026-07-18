<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;

class CriminalCaseAcceptRequest extends FormRequest
{
    use AuthorizesCriminalCase;

    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.user'), 403);

        $case = app(CriminalCaseRepository::class)->getById($this->route('id'));

        if (!$case) {
            return true;
        }

        $this->assertCanActOnCase($case, $this->user());
        abort_unless(
            in_array($case->status, ['allocated', 'reallocated']),
            403,
            'This case must be allocated to a lawyer before it can be accepted or rejected.'
        );

        return true;
    }

    /**
     * No body fields — accepting a case is a plain state transition.
     */
    public function rules(): array
    {
        return [];
    }
}
