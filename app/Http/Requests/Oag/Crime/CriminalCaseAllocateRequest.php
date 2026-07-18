<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;

class CriminalCaseAllocateRequest extends FormRequest
{
    use AuthorizesCriminalCase;

    public function authorize(): bool
    {
        // Closing a pre-existing gap: this action previously had no
        // server-side role check at all (only hidden client-side for
        // non-admins) — allocation is a cm.admin action everywhere else in
        // this module (reallocateCase, the reallocate branch of Case Review
        // edit), so it belongs here too.
        abort_unless($this->user()->hasRole('cm.admin'), 403);

        $case = app(CriminalCaseRepository::class)->getById($this->route('id'));

        if (!$case) {
            return true;
        }

        $this->assertCaseIsActionable($case);

        return true;
    }

    public function rules(): array
    {
        return [
            'to_lawyer_id' => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'reallocation_reason' => 'nullable|string|max:1000',
            'reallocation_date' => 'required|date',
        ];
    }
}
