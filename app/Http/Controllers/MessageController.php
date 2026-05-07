<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::inbox(Auth::id())->get();
        $sentMessages = Message::sent(Auth::id())->get();

        return view('pages.messages.index', compact('messages', 'sentMessages'));
    }

    public function show(Message $message)
    {
        // Mark as read if the current user is the receiver
        if ($message->receiver_id === Auth::id() && ! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('pages.messages.show', compact('message'));
    }

    public function create(Request $request)
    {
        $userId = $request->get('user_id');
        $user = null;

        if ($userId) {
            $user = \App\Models\User::find($userId);
        }

        return view('pages.messages.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['sender_id'] = Auth::id();

        Message::create($validated);

        return redirect()->route('messages.index')->with('success', __('lms.message_sent'));
    }

    public function markAsRead(Message $message)
    {
        if ($message->receiver_id === Auth::id()) {
            $message->update(['is_read' => true]);
        }

        return back();
    }

    public function markAllAsRead()
    {
        Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', __('lms.all_marked_as_read'));
    }

    public function destroy(Message $message)
    {
        // Only allow delete if user is sender or receiver
        if ($message->sender_id === Auth::id() || $message->receiver_id === Auth::id()) {
            $message->delete();

            return back()->with('success', __('lms.message_deleted'));
        }

        abort(403);
    }
}
