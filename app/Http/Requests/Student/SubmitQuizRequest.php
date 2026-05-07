<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SubmitQuizRequest extends FormRequest
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
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quiz_id.required' => 'The quiz is required.',
            'quiz_id.exists' => 'The selected quiz does not exist.',
            'answers.required' => 'The answers are required.',
            'answers.array' => 'The answers must be an array.',
            'answers.*.required' => 'Each answer is required.',
            'answers.*.max' => 'Each answer must not exceed 1000 characters.',
        ];
    }
}
