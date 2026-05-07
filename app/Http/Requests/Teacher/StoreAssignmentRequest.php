<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAssignmentRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'course_id' => 'required|exists:courses,id',
            'total_marks' => 'required|integer|min:1|max:1000',
            'due_date' => 'required|date|after_or_equal:now',
            'is_active' => 'boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'rubric' => 'nullable|array',
            'rubric.*.criteria' => 'required|string|max:255',
            'rubric.*.marks' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 5000 characters.',
            'course_id.required' => 'The course is required.',
            'course_id.exists' => 'The selected course does not exist.',
            'total_marks.required' => 'The total marks are required.',
            'total_marks.min' => 'The total marks must be at least 1.',
            'total_marks.max' => 'The total marks must not exceed 1000.',
            'due_date.required' => 'The due date is required.',
            'due_date.after_or_equal' => 'The due date must be now or later.',
            'attachment.file' => 'The attachment must be a file.',
            'attachment.mimes' => 'The attachment must be a file of type: pdf, doc, docx, zip.',
            'attachment.max' => 'The attachment must not be larger than 10240 kilobytes.',
            'rubric.*.criteria.required' => 'The criteria is required.',
            'rubric.*.criteria.max' => 'The criteria must not exceed 255 characters.',
            'rubric.*.marks.required' => 'The marks are required.',
            'rubric.*.marks.min' => 'The marks must be at least 1.',
        ];
    }
}
