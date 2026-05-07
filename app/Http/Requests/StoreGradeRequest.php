<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('teacher');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'assessment_id' => 'required|exists:assessments,id',
            'marks_obtained' => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:1000',
            'graded_at' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'The student is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'assessment_id.required' => 'The assessment is required.',
            'assessment_id.exists' => 'The selected assessment does not exist.',
            'marks_obtained.required' => 'The marks obtained are required.',
            'marks_obtained.numeric' => 'The marks obtained must be a number.',
            'marks_obtained.min' => 'The marks obtained must be at least 0.',
            'feedback.max' => 'The feedback must not exceed 1000 characters.',
            'graded_at.date' => 'The graded at must be a valid date.',
        ];
    }
}
