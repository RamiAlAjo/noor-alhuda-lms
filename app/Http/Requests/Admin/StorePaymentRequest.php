<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaymentRequest extends FormRequest
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
            'student_id' => 'required|exists:users,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'payment_method' => 'required|in:cash,card,bank_transfer,online,other',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,completed,failed,refunded',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'fee_id.exists' => 'The selected fee does not exist.',
            'amount.min' => 'The amount must be at least 0.01.',
            'amount.max' => 'The amount must not exceed 999999.99.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'transaction_id.max' => 'The transaction ID must not exceed 255 characters.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'notes.max' => 'The notes must not exceed 500 characters.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
