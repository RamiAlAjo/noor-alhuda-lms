<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAttendanceRequest extends FormRequest
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
            'course_offering_id' => 'required|exists:course_offerings,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|string|max:500',
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
            'course_offering_id.required' => 'The course offering is required.',
            'course_offering_id.exists' => 'The selected course offering does not exist.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be one of: present, absent, late, excused.',
            'remarks.max' => 'The remarks must not exceed 500 characters.',
        ];
    }
}
