<?php

namespace App\Http\Requests\Oag\Crime;

use App\Http\Controllers\Concerns\AuthorizesCriminalCase;
use App\Repositories\Oag\Crime\CriminalCaseRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriminalCaseUpdateRequest extends FormRequest
{
    use AuthorizesCriminalCase;

    public function authorize(): bool
    {
        $case = app(CriminalCaseRepository::class)->getById($this->route('criminalCase'));

        // Case-not-found is handled by the controller's own redirect, not a 403.
        if (!$case) {
            return true;
        }

        $this->assertCaseIsActionable($case);

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_file_number'      => [
                'required', 'string', 'max:255',
                Rule::unique('cases', 'case_file_number')->ignore($this->route('criminalCase')),
            ],
            'date_file_received'    => 'required|date',
            'case_name'             => 'required|string|max:255',
            'date_of_incident'      => 'nullable|date',
            'date_file_closed'      => 'nullable|date',
            'reason_for_closure_id' => 'nullable|exists:reasons_for_closure,id',
            'lawyer_id'             => ['required', 'exists:users,id', new \App\Rules\UserHasRole('cm.user')],
            'island_id'             => 'required|exists:islands,id',
            'court_case_number'     => 'nullable|string|max:255',
        ];
    }
}
