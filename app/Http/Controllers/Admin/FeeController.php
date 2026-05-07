<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    /**
     * Display all fees.
     */
    public function index(): View
    {
        // Get stats
        $stats = [
            'total_revenue' => Payment::whereIn('status', ['completed', 'approved'])->sum('amount') ?? 0,
            'pending_payments' => Payment::where('status', 'pending')->count() ?? 0,
            'overdue' => StudentFee::whereIn('status', ['pending', 'partial'])->where('due_date', '<', now())->count() ?? 0,
        ];

        $fees = Fee::with('major')->orderBy('created_at', 'desc')->get();

        return view('pages.admin.fees.index', compact('fees', 'stats'));
    }

    /**
     * Create a new fee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'fee_type' => 'required|in:tuition,registration,library,lab,other',
            'major_id' => 'nullable|exists:majors,id',
            'academic_year' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Fee::create($request->all());

        return back()->with('success', __('lms::messages.fee_created'));
    }

    /**
     * Update a fee.
     */
    public function update(Request $request, Fee $fee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'fee_type' => 'required|in:tuition,registration,library,lab,other',
            'major_id' => 'nullable|exists:majors,id',
            'academic_year' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $fee->update($request->all());

        return back()->with('success', __('lms::messages.fee_updated'));
    }

    /**
     * Delete a fee.
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();

        return back()->with('success', __('lms::messages.fee_deleted'));
    }

    /**
     * Display all payments.
     */
    public function payments(Request $request): View
    {
        $query = Payment::select('id', 'student_id', 'student_fee_id', 'amount', 'payment_method', 'payment_gateway', 'status', 'gateway_transaction_id', 'transaction_id', 'created_at')
            ->with(['student' => function ($q) {
                $q->select('id', 'user_id', 'name', 'email');
            }, 'studentFee' => function ($q) {
                $q->select('id', 'fee_id', 'amount', 'paid_amount', 'status');
            }, 'studentFee.fee' => function ($q) {
                $q->select('id', 'name', 'amount');
            }]);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.admin.payments.index', compact('payments'));
    }

    /**
     * Approve a payment.
     */
    public function approvePayment(Payment $payment)
    {
        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', __('lms::messages.payment_approved'));
    }

    /**
     * Reject a payment.
     */
    public function rejectPayment(Payment $payment)
    {
        $payment->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', __('lms::messages.payment_rejected'));
    }

    /**
     * Create payment record.
     */
    public function storePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_id' => 'required|exists:fees,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:stripe,paypal,cash,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Find or create student fee
        $studentFee = StudentFee::firstOrCreate(
            ['student_id' => $request->student_id, 'fee_id' => $request->fee_id],
            ['amount' => Fee::find($request->fee_id)->amount ?? 0, 'paid_amount' => 0, 'status' => 'pending']
        );

        Payment::create([
            'student_id' => $request->student_id,
            'student_fee_id' => $studentFee->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_gateway' => 'manual',
            'transaction_id' => $request->transaction_id,
            'status' => 'completed',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.payments.index')->with('success', 'Payment recorded successfully.');
    }

    /**
     * Update the specified payment.
     */
    public function updatePayment(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|in:cash,credit_card,bank_transfer,online',
            'transaction_id' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'notes' => 'sometimes|string',
        ]);

        $payment->update($request->only([
            'amount', 'payment_method', 'transaction_id', 'status', 'notes'
        ]));

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment.
     */
    public function deletePayment(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully.');
    }

    /**
     * Display financial reports.
     */
    public function reports(Request $request): View
    {
        $semesterId = $request->get('semester_id');
        $reportType = $request->get('report_type', 'summary');

        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();

        $report = [];

        if ($semesterId) {
            switch ($reportType) {
                case 'summary':
                    $report = $this->getFinancialSummary($semesterId);
                    break;
                case 'outstanding':
                    $report = $this->getOutstandingBalances($semesterId);
                    break;
                case 'collections':
                    $report = $this->getCollectionReport($semesterId);
                    break;
            }
        }

        return view('pages.admin.fees.reports', compact('semesters', 'report', 'semesterId', 'reportType'));
    }

    /**
     * Export fees to CSV.
     */
    public function exportFees(Request $request)
    {
        $query = Fee::with('major');

        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        $fees = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fees_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($fees) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Name (Arabic)',
                'Amount',
                'Type',
                'Major',
                'Academic Year',
                'Due Date',
                'Description',
                'Created At',
            ]);

            foreach ($fees as $fee) {
                fputcsv($file, [
                    $fee->id,
                    $fee->name,
                    $fee->name_ar,
                    $fee->amount,
                    $fee->fee_type,
                    $fee->major?->name,
                    $fee->academic_year,
                    $fee->due_date?->format('Y-m-d'),
                    $fee->description,
                    $fee->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export payments to CSV.
     */
    public function exportPayments(Request $request)
    {
        $query = Payment::with(['student', 'fee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Student ID',
                'Student Name',
                'Student Email',
                'Fee Name',
                'Amount',
                'Payment Method',
                'Transaction ID',
                'Status',
                'Payment Date',
                'Approved By',
                'Approved At',
                'Notes',
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->student->id,
                    $payment->student->name,
                    $payment->student->email,
                    $payment->fee?->name,
                    $payment->amount,
                    $payment->payment_method,
                    $payment->transaction_id,
                    $payment->status,
                    $payment->payment_date?->format('Y-m-d'),
                    $payment->approver?->name,
                    $payment->approved_at?->format('Y-m-d H:i:s'),
                    $payment->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export outstanding balances to CSV.
     */
    public function exportOutstandingBalances(Request $request)
    {
        $semesterId = $request->get('semester_id');

        $query = StudentFee::with(['student', 'fee'])
            ->where('status', '!=', 'paid');

        if ($semesterId) {
            $query->whereHas('fee', function ($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            });
        }

        $outstandingFees = $query->orderBy('due_date')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="outstanding_balances_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($outstandingFees) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Student Email',
                'Fee Name',
                'Total Amount',
                'Amount Paid',
                'Outstanding Balance',
                'Due Date',
                'Status',
                'Days Overdue',
            ]);

            foreach ($outstandingFees as $studentFee) {
                $outstanding = $studentFee->amount - ($studentFee->paid_amount ?? 0);
                $daysOverdue = $studentFee->due_date && $studentFee->due_date->isPast()
                    ? now()->diffInDays($studentFee->due_date)
                    : 0;

                fputcsv($file, [
                    $studentFee->student->id,
                    $studentFee->student->name,
                    $studentFee->student->email,
                    $studentFee->fee?->name,
                    $studentFee->amount,
                    $studentFee->paid_amount ?? 0,
                    $outstanding,
                    $studentFee->due_date?->format('Y-m-d'),
                    $studentFee->status,
                    $daysOverdue,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export financial summary report.
     */
    public function exportFinancialSummary(Request $request)
    {
        $semesterId = $request->get('semester_id');

        if (! $semesterId) {
            return back()->with('error', __('lms.select_semester'));
        }

        $report = $this->getFinancialSummary($semesterId);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="financial_summary_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Financial Summary Report']);
            fputcsv($file, ['Semester', $report['semester']->name ?? '']);
            fputcsv($file, []);

            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Fees Assigned', $report['total_fees']]);
            fputcsv($file, ['Total Amount', $report['total_amount']]);
            fputcsv($file, ['Total Collected', $report['total_collected']]);
            fputcsv($file, ['Total Outstanding', $report['total_outstanding']]);
            fputcsv($file, ['Collection Rate', $report['collection_rate'].'%']);
            fputcsv($file, []);

            fputcsv($file, ['By Fee Type']);
            fputcsv($file, ['Type', 'Count', 'Total Amount', 'Collected', 'Outstanding']);

            foreach ($report['by_type'] as $type => $data) {
                fputcsv($file, [
                    $type,
                    $data['count'],
                    $data['total'],
                    $data['collected'],
                    $data['outstanding'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get financial summary for a semester.
     */
    private function getFinancialSummary(int $semesterId): array
    {
        $semester = Semester::find($semesterId);

        // Get fee IDs for this semester
        $feeIds = Fee::where('semester_id', $semesterId)->pluck('id');

        if ($feeIds->isEmpty()) {
            return [
                'semester' => $semester,
                'total_fees' => 0,
                'total_amount' => 0,
                'total_collected' => 0,
                'total_outstanding' => 0,
                'collection_rate' => 0,
                'by_type' => [],
            ];
        }

        // Use simple aggregation with whereIn
        $totalFees = StudentFee::whereIn('fee_id', $feeIds)->count();
        $totalAmount = StudentFee::whereIn('fee_id', $feeIds)->sum('amount');
        $totalCollected = StudentFee::whereIn('fee_id', $feeIds)->sum('paid_amount');
        $totalOutstanding = $totalAmount - $totalCollected;
        $collectionRate = $totalAmount > 0 ? round(($totalCollected / $totalAmount) * 100, 2) : 0;

        // By type - get fee IDs for each type
        $byType = [];
        $feeTypes = ['tuition', 'registration', 'library', 'lab', 'other'];
        foreach ($feeTypes as $type) {
            $typeFeeIds = Fee::whereIn('id', $feeIds)
                ->where('fee_type', $type)
                ->pluck('id');

            if ($typeFeeIds->isEmpty()) {
                $byType[$type] = ['count' => 0, 'total' => 0, 'collected' => 0, 'outstanding' => 0];
            } else {
                $byType[$type] = [
                    'count' => StudentFee::whereIn('fee_id', $typeFeeIds)->count(),
                    'total' => StudentFee::whereIn('fee_id', $typeFeeIds)->sum('amount'),
                    'collected' => StudentFee::whereIn('fee_id', $typeFeeIds)->sum('paid_amount'),
                    'outstanding' => StudentFee::whereIn('fee_id', $typeFeeIds)->sum('amount') - StudentFee::whereIn('fee_id', $typeFeeIds)->sum('paid_amount'),
                ];
            }
        }

        return [
            'semester' => $semester,
            'total_fees' => $totalFees,
            'total_amount' => $totalAmount,
            'total_collected' => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'collection_rate' => $collectionRate,
            'by_type' => $byType,
        ];
    }

    /**
     * Get outstanding balances for a semester.
     */
    private function getOutstandingBalances(int $semesterId): array
    {
        $semester = Semester::find($semesterId);

        // Get fee IDs for this semester
        $feeIds = Fee::where('semester_id', $semesterId)->pluck('id');

        if ($feeIds->isEmpty()) {
            return [
                'semester' => $semester,
                'outstanding_fees' => collect(),
                'by_student' => collect(),
                'total_outstanding' => 0,
                'students_with_outstanding' => 0,
            ];
        }

        // Get unpaid student fees with limit
        $outstandingFees = StudentFee::whereIn('fee_id', $feeIds)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->limit(200) // Limit to avoid timeout
            ->get();

        $byStudent = $outstandingFees->groupBy('student_id')->map(function ($fees) {
            return [
                'student' => $fees->first()->student,
                'total_outstanding' => $fees->sum('amount') - $fees->sum('paid_amount'),
                'fees_count' => $fees->count(),
                'overdue_count' => $fees->filter(function ($f) {
                    return $f->due_date && $f->due_date->isPast();
                })->count(),
            ];
        })->sortByDesc('total_outstanding');

        return [
            'semester' => $semester,
            'outstanding_fees' => $outstandingFees,
            'by_student' => $byStudent,
            'total_outstanding' => $outstandingFees->sum('amount') - $outstandingFees->sum('paid_amount'),
            'students_with_outstanding' => $byStudent->count(),
        ];
    }

    /**
     * Get collection report for a semester.
     */
    private function getCollectionReport(int $semesterId): array
    {
        $semester = Semester::find($semesterId);

        // Get fee IDs for this semester
        $feeIds = Fee::where('semester_id', $semesterId)->pluck('id');

        if ($feeIds->isEmpty()) {
            return [
                'semester' => $semester,
                'payments' => collect(),
                'total_collected' => 0,
                'by_method' => [],
                'by_date' => [],
                'payments_count' => 0,
            ];
        }

        // Get payments for this semester
        $payments = Payment::whereIn('fee_id', $feeIds)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(100) // Limit to recent 100 to avoid timeout
            ->get();

        $totalCollected = $payments->sum('amount');

        $byMethod = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        $byDate = $payments->groupBy(function ($p) {
            return $p->payment_date ? $p->payment_date->format('Y-m-d') : 'Unknown';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        return [
            'semester' => $semester,
            'payments' => $payments,
            'total_collected' => $totalCollected,
            'by_method' => $byMethod,
            'by_date' => $byDate,
            'payments_count' => $payments->count(),
        ];
    }
}
