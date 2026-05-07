<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends Controller
{
    /**
     * Display user's notes.
     */
    public function index(): View
    {
        $notes = Note::where('user_id', auth()->id())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('pages.notes.index', compact('notes'));
    }

    /**
     * Store a new note.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        Note::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'color' => $request->color ?? 'white',
        ]);

        return back()->with('success', __('lms::messages.note_created'));
    }

    /**
     * Update a note.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->update($request->all());

        return back()->with('success', __('lms::messages.note_updated'));
    }

    /**
     * Delete a note.
     */
    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        return back()->with('success', __('lms::messages.note_deleted'));
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->update(['is_pinned' => ! $note->is_pinned]);

        return back();
    }
}
