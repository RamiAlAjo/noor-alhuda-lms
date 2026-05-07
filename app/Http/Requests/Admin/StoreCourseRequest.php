<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCourseRequest extends FormRequest
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
        return [
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'credits' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'major_id' => 'nullable|exists:majors,id',
            'level' => 'required|in:undergraduate,graduate,postgraduate',
            'semester' => 'required|in:fall,spring,summer',
            'year' => 'required|integer|min:2020|max:2030',
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
            'code.unique' => 'A course with this code already exists.',
            'department_id.exists' => 'The selected department does not exist.',
            'major_id.exists' => 'The selected major does not exist.',
            'prerequisites.*.exists' => 'One or more selected prerequisites do not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
