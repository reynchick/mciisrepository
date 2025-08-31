<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
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
            'facultyID' => ['required', 'string', 'max:255', 'unique:faculties,facultyID'],
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'unique:faculties,email',
                'regex:/^[^@]+@usep\.edu\.ph$/'
            ],
            'ORCID' => ['nullable', 'string', 'max:255'],
            'contactNumber' => ['nullable', 'string', 'max:255'],
            'educationalAttainment' => ['nullable', 'string', 'max:255'],
            'fieldOfSpecialization' => ['nullable', 'string'],
            'researchInterest' => ['nullable', 'string'],
        ];
    }
}
