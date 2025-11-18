<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CustomEmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::find($this->route('id'));
        
        if (!$user) {
            return false;
        }

        if (!hash_equals((string) $user->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get the user model from the request.
     */
    public function getUserModel(): User
    {
        $user = User::find($this->route('id'));
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Invalid verification link.'
            ]);
        }

        return $user;
    }
}
