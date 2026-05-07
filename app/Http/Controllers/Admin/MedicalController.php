<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalController extends Controller
{
    /**
     * Display all medical records.
     */
    public function index(Request $request): View
    {
        $query = User::role('student')->with(['medicalRecord', 'profile']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);

        return view('pages.admin.medical.index', compact('students'));
    }

    /**
     * Display a specific student's medical record.
     */
    public function show(User $student): View
    {
        if (! $student->hasRole('student')) {
            abort(404);
        }

        $student->load('medicalRecord');

        return view('pages.admin.medical.show', compact('student'));
    }

    /**
     * Create or update medical record.
     */
    public function update(Request $request, User $student)
    {
        if (! $student->hasRole('student')) {
            abort(404);
        }

        $request->validate([
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'doctor_name' => 'nullable|string|max:255',
            'doctor_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        MedicalRecord::updateOrCreate(
            ['student_id' => $student->id],
            $request->all()
        );

        return back()->with('success', __('lms::messages.medical_record_updated'));
    }

    /**
     * Delete medical record.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();

        return back()->with('success', __('lms::messages.medical_record_deleted'));
    }
}
