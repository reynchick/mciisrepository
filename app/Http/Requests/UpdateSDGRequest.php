<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSDGRequest extends FormRequest
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
        $sdgId = $this->route('s_d_g') ?? $this->route('sdg');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('sdgs', 'name')->ignore($sdgId)],
            'description' => ['nullable', 'string'],
        ];
    }
}