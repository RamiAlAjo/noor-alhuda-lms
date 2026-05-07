<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display course announcements.
     */
    public function index(CourseSection $section): View
    {
        return view('pages.teacher.courses.announcements', compact('section'));
    }

    /**
     * Store a new announcement.
     */
    public function store(Request $request, CourseSection $section)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $section->announcements()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'is_pinned' => $request->is_pinned ?? false,
            'target_type' => 'section',
            'target_section_id' => $section->id,
            'is_published' => true,
        ]);

        return back()->with('success', __('Announcement posted successfully'));
    }

    /**
     * Toggle pinned status.
     */
    public function togglePin(CourseSection $section, Announcement $announcement)
    {
        $announcement->update(['is_pinned' => ! $announcement->is_pinned]);

        return back();
    }

    /**
     * Delete announcement.
     */
    public function destroy(CourseSection $section, Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', __('Announcement deleted'));
    }
}
