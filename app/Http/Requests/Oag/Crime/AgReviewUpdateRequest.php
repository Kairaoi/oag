<?php

namespace App\Http\Requests\Oag\Crime;

use Illuminate\Foundation\Http\FormRequest;

class AgReviewUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        abort_unless($this->user()->hasRole('cm.ag'), 403);

        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ag_decision' => 'required|in:approved,rejected',
            'decision_date' => 'required|date',
            'ag_comments' => 'nullable|string',
        ];
    }
}
