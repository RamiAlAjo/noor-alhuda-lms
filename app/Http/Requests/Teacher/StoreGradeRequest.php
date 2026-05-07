<?php

namespace App\Http\Requests\Teacher;

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
            'course_id' => 'required|exists:courses,id',
            'assessment_type' => 'required|string|max:50',
            'assessment_name' => 'required|string|max:100',
            'score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:1|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
            'graded_at' => 'required|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'course_id.exists' => 'The selected course does not exist.',
            'score.max' => 'The score cannot exceed 100.',
            'max_score.max' => 'The maximum score cannot exceed 100.',
            'weight.max' => 'The weight cannot exceed 100.',
        ];
    }
}
