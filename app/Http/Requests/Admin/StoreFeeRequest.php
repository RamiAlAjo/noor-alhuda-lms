<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFeeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'fee_type' => 'required|in:tuition,examination,laboratory,library,hostel,transport,other',
            'course_id' => 'nullable|exists:courses,id',
            'academic_year' => 'required|string|max:9',
            'semester' => 'required|in:fall,spring,summer',
            'due_date' => 'required|date|after:today',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'The name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'amount.min' => 'The amount must be at least 0.',
            'amount.max' => 'The amount must not exceed 999999.99.',
            'fee_type.in' => 'The selected fee type is invalid.',
            'course_id.exists' => 'The selected course does not exist.',
            'academic_year.max' => 'The academic year must not exceed 9 characters.',
            'semester.in' => 'The selected semester is invalid.',
            'due_date.after' => 'The due date must be after today.',
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
