<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompiledReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'MCIIS Staff']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_type_id' => ['required', 'exists:report_types,id'],
            'report_format_id' => ['required', 'exists:report_formats,id'],
            'filters_applied' => ['nullable', 'json'],
            'file_path' => ['required', 'string'],
        ];
    }
}
