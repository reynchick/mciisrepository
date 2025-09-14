<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResearchRequest extends FormRequest
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
        $researchId = $this->route('research');
        
        return [
            'research_title' => ['required', 'string', 'max:255', Rule::unique('research', 'research_title')->ignore($researchId)],
            'research_adviser' => ['nullable', 'exists:faculties,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'published_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'published_year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'research_abstract' => ['required', 'string'],
            'research_approval_sheet' => ['nullable', 'file', 'image', 'max:2048'],
            'research_manuscript' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'keywords' => ['required', 'array', 'min:1'],
            'keywords.*' => ['exists:keywords,id'],
            'archive_reason' => ['nullable', 'string', 'required_with:archived_at'],
        ];
    }
}
