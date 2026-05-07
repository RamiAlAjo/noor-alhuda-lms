<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccommodationType;
use App\Models\StudentAccommodation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccommodationController extends Controller
{
    /**
     * Display accommodation types.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'types');

        if ($tab === 'types') {
            $types = AccommodationType::withCount('studentAccommodations')
                ->orderBy('category')
                ->orderBy('name')
                ->paginate(20);

            return view('pages.admin.accommodations.index', compact('types', 'tab'));
        }

        // Student accommodations tab
        $query = StudentAccommodation::with(['student', 'accommodationType', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $accommodations = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.admin.accommodations.index', compact('accommodations', 'tab'));
    }

    /**
     * Store a new accommodation type.
     */
    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:accommodation_types,code',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'default_settings' => 'nullable|array',
            'requires_documentation' => 'boolean',
        ]);

        AccommodationType::create($validated);

        return redirect()->route('admin.accommodations.index', ['tab' => 'types'])
            ->with('success', __('lms.accommodation_type_created'));
    }

    /**
     * Update an accommodation type.
     */
    public function updateType(Request $request, AccommodationType $accommodationType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:accommodation_types,code,'.$accommodationType->id,
            'description' => 'nullable|string',
            'category' => 'required|string',
            'default_settings' => 'nullable|array',
            'requires_documentation' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $accommodationType->update($validated);

        return redirect()->route('admin.accommodations.index', ['tab' => 'types'])
            ->with('success', __('lms.accommodation_type_updated'));
    }

    /**
     * Delete an accommodation type.
     */
    public function destroyType(AccommodationType $accommodationType)
    {
        if ($accommodationType->studentAccommodations()->exists()) {
            return redirect()->route('admin.accommodations.index', ['tab' => 'types'])
                ->with('error', __('lms.cannot_delete_accommodation_type_in_use'));
        }

        $accommodationType->delete();

        return redirect()->route('admin.accommodations.index', ['tab' => 'types'])
            ->with('success', __('lms.accommodation_type_deleted'));
    }

    /**
     * Show form to assign accommodation to student.
     */
    public function createStudentAccommodation(Request $request)
    {
        $studentId = $request->get('student_id');
        $student = $studentId ? User::findOrFail($studentId) : null;
        $types = AccommodationType::active()->orderBy('category')->orderBy('name')->get();
        $students = User::role('student')->orderBy('name')->get();
        $categories = AccommodationType::getCategories();

        return view('pages.admin.accommodations.create', compact('student', 'types', 'students', 'categories'));
    }

    /**
     * Store a student accommodation.
     */
    public function storeStudentAccommodation(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'accommodation_type_id' => 'required|exists:accommodation_types,id',
            'notes' => 'nullable|string',
            'custom_settings' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'documentation' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $accommodationType = AccommodationType::findOrFail($validated['accommodation_type_id']);

        // Check if student already has this accommodation
        $existing = StudentAccommodation::where('student_id', $validated['student_id'])
            ->where('accommodation_type_id', $validated['accommodation_type_id'])
            ->where('status', '!=', StudentAccommodation::STATUS_EXPIRED)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', __('lms.student_already_has_accommodation'))
                ->withInput();
        }

        // Handle documentation upload
        $documentationPath = null;
        if ($request->hasFile('documentation')) {
            $documentationPath = $request->file('documentation')
                ->store('accommodations/documentation', 'public');
        }

        $accommodation = StudentAccommodation::create([
            'student_id' => $validated['student_id'],
            'accommodation_type_id' => $validated['accommodation_type_id'],
            'notes' => $validated['notes'] ?? null,
            'custom_settings' => $validated['custom_settings'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'documentation_path' => $documentationPath,
            'status' => StudentAccommodation::STATUS_ACTIVE,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.accommodations.index', ['tab' => 'students'])
            ->with('success', __('lms.student_accommodation_assigned'));
    }

    /**
     * Show a student accommodation.
     */
    public function showStudentAccommodation(StudentAccommodation $studentAccommodation)
    {
        $studentAccommodation->load(['student', 'accommodationType', 'approver', 'quizAccommodations.assessment']);

        return view('pages.admin.accommodations.show', compact('studentAccommodation'));
    }

    /**
     * Show form to edit student accommodation.
     */
    public function editStudentAccommodation(StudentAccommodation $studentAccommodation)
    {
        $types = AccommodationType::active()->orderBy('category')->orderBy('name')->get();
        $categories = AccommodationType::getCategories();

        return view('pages.admin.accommodations.edit', compact('studentAccommodation', 'types', 'categories'));
    }

    /**
     * Update a student accommodation.
     */
    public function updateStudentAccommodation(Request $request, StudentAccommodation $studentAccommodation)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'custom_settings' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
            'documentation' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        // Handle documentation upload
        if ($request->hasFile('documentation')) {
            // Delete old documentation
            if ($studentAccommodation->documentation_path) {
                Storage::disk('public')->delete($studentAccommodation->documentation_path);
            }
            $validated['documentation_path'] = $request->file('documentation')
                ->store('accommodations/documentation', 'public');
        }

        $studentAccommodation->update($validated);

        return redirect()->route('admin.accommodations.show-student', $studentAccommodation)
            ->with('success', __('lms.student_accommodation_updated'));
    }

    /**
     * Delete a student accommodation.
     */
    public function destroyStudentAccommodation(StudentAccommodation $studentAccommodation)
    {
        // Delete documentation
        if ($studentAccommodation->documentation_path) {
            Storage::disk('public')->delete($studentAccommodation->documentation_path);
        }

        $studentAccommodation->delete();

        return redirect()->route('admin.accommodations.index', ['tab' => 'students'])
            ->with('success', __('lms.student_accommodation_deleted'));
    }

    /**
     * Get accommodations for a specific student (API).
     */
    public function getStudentAccommodations(User $student)
    {
        $accommodations = StudentAccommodation::with('accommodationType')
            ->where('student_id', $student->id)
            ->active()
            ->get();

        return response()->json($accommodations);
    }

    /**
     * Export accommodations to CSV.
     */
    public function export(Request $request)
    {
        $query = StudentAccommodation::with(['student', 'accommodationType']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $accommodations = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_accommodations_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($accommodations) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Student ID',
                'Student Name',
                'Student Email',
                'Accommodation Type',
                'Category',
                'Status',
                'Start Date',
                'End Date',
                'Approved By',
                'Approved At',
                'Notes',
            ]);

            foreach ($accommodations as $accommodation) {
                fputcsv($file, [
                    $accommodation->student->id,
                    $accommodation->student->name,
                    $accommodation->student->email,
                    $accommodation->accommodationType->name,
                    $accommodation->accommodationType->category,
                    $accommodation->status,
                    $accommodation->start_date?->format('Y-m-d'),
                    $accommodation->end_date?->format('Y-m-d'),
                    $accommodation->approver?->name,
                    $accommodation->approved_at?->format('Y-m-d H:i:s'),
                    $accommodation->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
