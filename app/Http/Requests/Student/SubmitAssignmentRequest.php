<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SubmitAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('student');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'submission_text' => 'nullable|string|max:10000',
            'attachment' => 'required_without:submission_text|file|mimes:pdf,doc,docx,zip|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assignment_id.required' => 'The assignment is required.',
            'assignment_id.exists' => 'The selected assignment does not exist.',
            'submission_text.max' => 'The submission text must not exceed 10000 characters.',
            'attachment.required_without' => 'The attachment is required when submission text is not provided.',
            'attachment.file' => 'The attachment must be a file.',
            'attachment.mimes' => 'The attachment must be a file of type: pdf, doc, docx, zip.',
            'attachment.max' => 'The attachment must not be larger than 10240 kilobytes.',
        ];
    }
}
