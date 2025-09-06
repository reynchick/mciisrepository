<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacultyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change this to your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $facultyId = $this->route('faculty'); // Assuming your route parameter is 'faculty'
        
        return [
            'faculty_id' => ['required', 'string', 'max:255', Rule::unique('faculties', 'faculty_id')->ignore($facultyId)],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('faculties', 'email')->ignore($facultyId),
                'regex:/^[^@]+@usep\.edu\.ph$/'
            ],
            'orcid' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'educational_attainment' => ['nullable', 'string', 'max:255'],
            'field_of_specialization' => ['nullable', 'string'],
            'research_interest' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'faculty_id.unique' => 'This Faculty ID is already taken.',
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
            'email.unique' => 'This email is already registered.',
        ];
    }
}
