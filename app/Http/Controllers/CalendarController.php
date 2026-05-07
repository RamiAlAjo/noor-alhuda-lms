<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = CalendarEvent::where('user_id', Auth::id())
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();

        return view('pages.calendar.index', compact('events', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'event_type' => 'nullable|in:personal,exam,assignment,class,meeting,other',
            'is_all_day' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['end_date'] = $validated['end_date'] ?? $validated['start_date'];

        CalendarEvent::create($validated);

        return back()->with('success', __('lms.event_created'));
    }

    public function update(Request $request, CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'event_type' => 'nullable|in:personal,exam,assignment,class,meeting,other',
            'is_all_day' => 'nullable|boolean',
        ]);

        $event->update($validated);

        return back()->with('success', __('lms.event_updated'));
    }

    public function destroy(CalendarEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();

        return back()->with('success', __('lms.event_deleted'));
    }

    public function getEvents()
    {
        $events = CalendarEvent::where('user_id', Auth::id())
            ->where('start_date', '>=', now()->subMonth())
            ->get();

        return response()->json($events);
    }
}
