<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuizRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
            'course_id' => 'required|exists:courses,id',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'total_marks' => 'required|integer|min:1|max:1000',
            'passing_marks' => 'required|integer|min:0|max:total_marks',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:1000',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.options' => 'required_if:questions.*.question_type,multiple_choice|array|min:2',
            'questions.*.options.*' => 'required|string|max:500',
            'questions.*.correct_answer' => 'required|string|max:500',
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
            'description.max' => 'The description must not exceed 1000 characters.',
            'course_id.required' => 'The course is required.',
            'course_id.exists' => 'The selected course does not exist.',
            'duration_minutes.required' => 'The duration is required.',
            'duration_minutes.min' => 'The duration must be at least 1 minute.',
            'duration_minutes.max' => 'The duration must not exceed 480 minutes.',
            'total_marks.required' => 'The total marks are required.',
            'total_marks.min' => 'The total marks must be at least 1.',
            'total_marks.max' => 'The total marks must not exceed 1000.',
            'passing_marks.required' => 'The passing marks are required.',
            'passing_marks.min' => 'The passing marks must be at least 0.',
            'passing_marks.max' => 'The passing marks must not exceed the total marks.',
            'start_date.required' => 'The start date is required.',
            'start_date.after_or_equal' => 'The start date must be now or later.',
            'end_date.required' => 'The end date is required.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'questions.required' => 'The questions are required.',
            'questions.min' => 'You must add at least 1 question.',
            'questions.*.question_text.required' => 'The question text is required.',
            'questions.*.question_text.max' => 'The question text must not exceed 1000 characters.',
            'questions.*.question_type.required' => 'The question type is required.',
            'questions.*.question_type.in' => 'The question type must be one of: multiple_choice, true_false, short_answer.',
            'questions.*.marks.required' => 'The marks are required.',
            'questions.*.marks.min' => 'The marks must be at least 1.',
            'questions.*.options.required_if' => 'The options are required for multiple choice questions.',
            'questions.*.options.min' => 'You must add at least 2 options.',
            'questions.*.options.*.required' => 'Each option is required.',
            'questions.*.options.*.max' => 'Each option must not exceed 500 characters.',
            'questions.*.correct_answer.required' => 'The correct answer is required.',
            'questions.*.correct_answer.max' => 'The correct answer must not exceed 500 characters.',
        ];
    }
}
