<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements.
     */
    public function index(): View
    {
        $announcements = Announcement::with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.admin.announcements.index', compact('announcements'));
    }

    /**
     * Create announcement form.
     */
    public function create(): View
    {
        $sections = CourseSection::with('course')->get();

        return view('pages.admin.announcements.create', compact('sections'));
    }

    /**
     * Store announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:global,faculty,department,course,section',
            'target_faculty_id' => 'nullable|exists:faculties,id',
            'target_department_id' => 'nullable|exists:departments,id',
            'target_offering_id' => 'nullable|exists:course_offerings,id',
            'target_course_id' => 'nullable|exists:courses,id',
            'target_section_id' => 'nullable|exists:course_sections,id',
            'is_published' => 'boolean',
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_type' => $request->target_type,
            'target_faculty_id' => $request->target_faculty_id,
            'target_department_id' => $request->target_department_id,
            'target_offering_id' => $request->target_offering_id,
            'target_course_id' => $request->target_course_id,
            'user_id' => auth()->id(),
            'is_published' => $request->is_published ?? false,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', __('Announcement created successfully!'));
    }

    /**
     * Edit announcement form.
     */
    public function edit(Announcement $announcement): View
    {
        $sections = CourseSection::with('course')->get();

        return view('pages.admin.announcements.edit', compact('announcement', 'sections'));
    }

    /**
     * Update announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:global,faculty,department,course,section',
            'target_faculty_id' => 'nullable|exists:faculties,id',
            'target_department_id' => 'nullable|exists:departments,id',
            'target_offering_id' => 'nullable|exists:course_offerings,id',
            'target_course_id' => 'nullable|exists:courses,id',
            'target_section_id' => 'nullable|exists:course_sections,id',
            'is_published' => 'boolean',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'target_type' => $request->target_type,
            'target_faculty_id' => $request->target_faculty_id,
            'target_department_id' => $request->target_department_id,
            'target_offering_id' => $request->target_offering_id,
            'target_course_id' => $request->target_course_id,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', __('lms::messages.announcement_updated'));
    }

    /**
     * Delete announcement.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', __('lms::messages.announcement_deleted'));
    }

    /**
     * Toggle pinned status.
     */
    public function togglePin(Announcement $announcement)
    {
        $announcement->update(['is_pinned' => ! $announcement->is_pinned]);

        return back();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Announcement $announcement)
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back();
    }
}
