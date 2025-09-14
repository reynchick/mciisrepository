<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'research_title' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('research', 'research_title')->whereNull('archived_at') // Only check against non-archived research
            ],
            'research_adviser' => ['nullable', 'exists:faculties,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'published_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'published_year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'research_abstract' => ['required', 'string'],
            'research_approval_sheet' => ['nullable', 'file', 'image', 'max:2048'], // 2MB max
            'research_manuscript' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB max
            'keywords' => ['required', 'array', 'min:1'],
            'keywords.*' => ['exists:keywords,id'],
            'researchers' => ['required', 'array', 'min:1'],
            'researchers.*.first_name' => ['required', 'string', 'max:255'],
            'researchers.*.middle_name' => ['nullable', 'string', 'max:255'],
            'researchers.*.last_name' => ['required', 'string', 'max:255'],
            'researchers.*.email' => [
                'nullable', 
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@usep\.edu\.ph$/', // Ensures USeP email format
                'unique:researchers,email'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'research_title.unique' => 'This research title already exists in the repository.',
            'researchers.*.email.regex' => 'The researcher email must be a valid USeP email address (e.g., name@usep.edu.ph).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('research_title')) {
            $this->merge([
                'research_title' => trim($this->research_title), // Remove extra whitespace for proper uniqueness check
            ]);
        }
    }
}
