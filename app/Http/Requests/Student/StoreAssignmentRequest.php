<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAssignmentRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'assignment_id' => 'required|exists:assignments,id',
            'submission_text' => 'nullable|string|max:5000',
            'submission_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,zip',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.exists' => 'The selected course does not exist.',
            'assignment_id.exists' => 'The selected assignment does not exist.',
            'submission_file.max' => 'The submission file must not exceed 10MB.',
            'submission_file.mimes' => 'The submission file must be a PDF, DOC, DOCX, TXT, or ZIP file.',
        ];
    }
}
