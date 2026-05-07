<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_fee_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_gateway',
        'gateway_transaction_id',
        'approved_by',
        'approved_at',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the fee associated with this payment through student_fee.
     */
    public function getFeeAttribute()
    {
        return $this->studentFee?->fee;
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
