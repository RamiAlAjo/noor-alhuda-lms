<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncementRequest extends FormRequest
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
            'content' => 'required|string|max:5000',
            'course_id' => 'required|exists:courses,id',
            'priority' => 'required|in:low,medium,high',
            'is_pinned' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.max' => 'The title must not exceed 255 characters.',
            'content.max' => 'The content must not exceed 5000 characters.',
            'course_id.exists' => 'The selected course does not exist.',
            'priority.in' => 'The selected priority is invalid.',
            'expires_at.after' => 'The expiration date must be after now.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_pinned' => $this->has('is_pinned'),
        ]);
    }
}
