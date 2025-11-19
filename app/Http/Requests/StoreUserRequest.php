<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }


    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'string', 'max:255', 'unique:users,student_id'],
            'faculty_id' => ['nullable', 'string', 'max:255', 'unique:users,faculty_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'email' => [
                'bail',
                'required',
                'email',
                'unique:users,email',
                'regex:/^[^@]+@usep\.edu\.ph$/'
            ],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['required', 'exists:roles,id'],
        ];
    }


    public function messages(): array
    {
        return [
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
            'role_ids.required' => 'At least one role must be selected.',
            'role_ids.*.exists' => 'One or more selected roles do not exist.',
        ];
    }


    protected function prepareForValidation(): void
    {
        foreach (['first_name', 'middle_name', 'last_name', 'contact_number', 'student_id', 'faculty_id'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                // Convert empty strings to null for student_id and faculty_id (they have UNIQUE constraints)
                if (in_array($field, ['student_id', 'faculty_id']) && $value === '') {
                    $this->merge([$field => null]);
                } else {
                    $this->merge([$field => $value]);
                }
            }
        }
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }
    }
}