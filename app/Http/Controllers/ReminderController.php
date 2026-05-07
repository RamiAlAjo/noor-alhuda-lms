<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('user_id', Auth::id())
            ->orderBy('remind_at', 'asc')
            ->get();

        return view('pages.reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_read'] = false;

        Reminder::create($validated);

        return back()->with('success', __('lms.reminder_created'));
    }

    public function markAsRead(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        $reminder->update(['is_read' => true]);

        return back();
    }

    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        $reminder->delete();

        return back()->with('success', __('lms.reminder_deleted'));
    }
}
