<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $courseId = $this->route('course') ?? $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses')->ignore($courseId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'credits' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'The course code is required.',
            'code.max' => 'The course code must not exceed 20 characters.',
            'code.unique' => 'The course code has already been taken.',
            'name.required' => 'The course name is required.',
            'name.max' => 'The course name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 2000 characters.',
            'credits.required' => 'The credits are required.',
            'credits.min' => 'The credits must be at least 1.',
            'credits.max' => 'The credits must not exceed 10.',
            'department_id.required' => 'The department is required.',
            'department_id.exists' => 'The selected department does not exist.',
            'prerequisites.*.exists' => 'The selected prerequisite does not exist.',
        ];
    }
}
