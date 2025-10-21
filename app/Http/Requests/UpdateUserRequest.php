<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        
        return [
            'student_id' => ['nullable', 'string', 'max:255', Rule::unique('users', 'student_id')->ignore($userId)],
            'faculty_id' => ['nullable', 'string', 'max:255', Rule::unique('users', 'faculty_id')->ignore($userId)],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'email' => [
                'bail',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
                'regex:/^[^@]+@usep\.edu\.ph$/'
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'must_change_password' => ['sometimes', 'boolean'],
            'is_temporary_password' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.exists' => 'Selected role does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['first_name', 'middle_name', 'last_name', 'contact_number', 'student_id', 'faculty_id'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim((string) $this->input($field))]);
            }
        }
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }
    }
}