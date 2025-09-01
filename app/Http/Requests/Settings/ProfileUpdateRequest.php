<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'studentID' => ['nullable', 'regex:/^\d{4}-\d{5}$/', Rule::unique('users', 'studentID')->ignore($this->user()->id)],
            'contactNumber' => ['required', 'regex:/^(09|\+63\s?9)\d{9}$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                'regex:/^[^@]+@usep\.edu\.ph$/',
            ],
            'role' => ['required', 'in:Administrator,MCIIS Staff,Faculty,Student'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'studentID.regex' => 'Student ID must be in format YYYY-NNNNN (e.g., 2023-00800)',
            'contactNumber.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +63 9XXXXXXXXX)',
            'email.regex' => 'Email must be a valid USeP email address ending with @usep.edu.ph',
        ];
    }
}
