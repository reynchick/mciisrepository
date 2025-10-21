<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKeywordRequest extends FormRequest
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
        $keywordId = $this->route('keyword');
        
        return [
            'keyword_name' => ['bail', 'required', 'string', 'max:255', Rule::unique('keywords', 'keyword_name')->ignore($keywordId)],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword_name.unique' => 'This keyword already exists.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('keyword_name')) {
            $this->merge(['keyword_name' => trim((string) $this->input('keyword_name'))]);
        }
    }
}
