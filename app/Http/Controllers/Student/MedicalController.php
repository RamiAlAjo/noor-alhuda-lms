<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAccommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MedicalController extends Controller
{
    /**
     * Display student's medical profile.
     */
    public function profile(): View
    {
        $user = Auth::user();
        $user->load(['medicalRecord', 'profile']);

        // Get active accommodations (table may not exist)
        try {
            $accommodations = StudentAccommodation::with('accommodationType')
                ->where('student_id', $user->id)
                ->active()
                ->get();
        } catch (\Exception $e) {
            $accommodations = collect();
        }

        return view('pages.student.medical.profile', compact('user', 'accommodations'));
    }

    /**
     * Show the form for editing medical profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $user->load(['profile', 'medicalRecord']);

        return view('pages.student.medical.edit', compact('user'));
    }

    /**
     * Update student's medical profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string|max:1000',
            'chronic_conditions' => 'nullable|string|max:1000',
            'current_medications' => 'nullable|string|max:1000',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'medical_notes' => 'nullable|string|max:2000',
        ]);

        // Update or create medical profile
        $user->medicalRecord()->updateOrCreate(
            ['student_id' => $user->id],
            $validated
        );

        return redirect()->route('student.medical.profile')
            ->with('success', __('lms.medical_profile_updated'));
    }
}
