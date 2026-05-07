<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'recipient_id.required' => 'The recipient is required.',
            'recipient_id.exists' => 'The selected recipient does not exist.',
            'subject.required' => 'The subject is required.',
            'subject.max' => 'The subject must not exceed 255 characters.',
            'body.required' => 'The message body is required.',
            'body.max' => 'The message body must not exceed 5000 characters.',
            'attachment.file' => 'The attachment must be a file.',
            'attachment.mimes' => 'The attachment must be a file of type: pdf, doc, docx, jpg, jpeg, png, zip.',
            'attachment.max' => 'The attachment must not be larger than 5120 kilobytes.',
        ];
    }
}
