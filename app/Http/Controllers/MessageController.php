<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Display user's conversations and messages.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'conversations');

        if ($tab === 'conversations') {
            $conversations = $this->messageService->getUserConversations($user->id);
            return view('pages.messages.index', compact('conversations', 'tab'));
        } else {
            // Legacy inbox view
            $messages = Message::inbox($user->id)->paginate(20);
            $sentMessages = Message::sent($user->id)->paginate(20);
            return view('pages.messages.index', compact('messages', 'sentMessages', 'tab'));
        }
    }

    /**
     * Show conversation messages.
     */
    public function showConversation(int $conversationId)
    {
        $user = Auth::user();
        $user->updateLastActivity(); // Update online status

        // Check if user has access to this conversation
        $conversation = Conversation::findOrFail($conversationId);
        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        $messages = $this->messageService->getConversationMessages($conversationId);
        $pinnedMessages = $this->messageService->getPinnedMessages($conversationId);

        // Mark conversation as read
        $this->messageService->markConversationAsRead($conversationId, $user->id);

        return view('pages.messages.conversation', compact('conversation', 'messages', 'pinnedMessages'));
    }

    /**
     * Show a single message (legacy support).
     */
    public function show(Message $message)
    {
        // Check permissions
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        // Mark as read if receiver
        if ($message->receiver_id === Auth::id() && !$message->is_read) {
            $message->markAsRead();
        }

        return view('pages.messages.show', compact('message'));
    }

    /**
     * Create new message/conversation.
     */
    public function create(Request $request)
    {
        $userId = $request->get('user_id');
        $conversationId = $request->get('conversation_id');

        $recipient = null;
        $conversation = null;
        $templates = MessageTemplate::getAvailableForUser(Auth::id());

        if ($userId) {
            $recipient = \App\Models\User::findOrFail($userId);
        }

        if ($conversationId) {
            $conversation = Conversation::findOrFail($conversationId);
            // Check if user is participant
            if (!$conversation->hasParticipant(Auth::id())) {
                abort(403);
            }
        }

        return view('pages.messages.create', compact('recipient', 'conversation', 'templates'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'receiver_id' => 'nullable|exists:users,id',
            'conversation_id' => 'nullable|exists:conversations,id',
            'template_id' => 'nullable|exists:message_templates,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:scheduled_at',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        try {
            $user = Auth::user();

            // Handle template-based messages
            if ($request->template_id) {
                $template = MessageTemplate::findOrFail($request->template_id);
                if (!$template->is_public && $template->created_by !== $user->id) {
                    abort(403);
                }

                $variables = $request->get('template_variables', []);
                $message = $this->messageService->sendTemplatedMessage(
                    $request->receiver_id,
                    $request->template_id,
                    $variables
                );
            } else {
                $options = [
                    'subject' => $request->subject,
                    'scheduled_at' => $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null,
                    'expires_at' => $request->expires_at ? \Carbon\Carbon::parse($request->expires_at) : null,
                    'priority' => $request->priority ?? Message::PRIORITY_NORMAL,
                    'tags' => $request->tags ?? [],
                ];

                // Handle attachments
                if ($request->hasFile('attachments')) {
                    $options['attachments'] = $request->file('attachments');
                }

                if ($request->conversation_id) {
                    // Reply in existing conversation
                    $message = $this->messageService->sendMessage(
                        $request->conversation_id,
                        $request->content,
                        $options
                    );
                } else {
                    // Create new direct message
                    $message = $this->messageService->sendDirectMessage(
                        $request->receiver_id,
                        $request->content,
                        $options
                    );
                }
            }

            $message = __('Message sent successfully!');
            if ($message->scheduled_at) {
                $message .= ' ' . __('It will be sent at') . ' ' . $message->scheduled_at->format('M j, Y \a\t g:i A');
            }

            return redirect()->route('messages.conversation', $message->conversation_id ?? $message->id)
                           ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    /**
     * Create a new conversation.
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Add current user to participants
            $participantIds = array_merge($request->participant_ids, [Auth::id()]);
            $participantIds = array_unique($participantIds);

            $conversation = $this->messageService->createConversation($participantIds, [
                'title' => $request->title,
            ]);

            // Send initial message
            $message = $this->messageService->sendMessage($conversation->id, $request->message);

            return redirect()->route('messages.conversation', $conversation->id)
                           ->with('success', __('Conversation created successfully!'));

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create conversation: ' . $e->getMessage());
        }
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        if ($message->receiver_id === Auth::id() || $message->conversation?->hasParticipant(Auth::id())) {
            $message->markAsRead();
        }

        return back();
    }

    /**
     * Mark conversation as read.
     */
    public function markConversationAsRead(int $conversationId)
    {
        $this->messageService->markConversationAsRead($conversationId, Auth::id());

        return back()->with('success', __('Conversation marked as read'));
    }

    /**
     * Mark all messages as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        // Mark all direct messages as read
        Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Mark all conversation messages as read
        $conversations = $user->conversations ?? [];
        foreach ($conversations as $conversation) {
            $this->messageService->markConversationAsRead($conversation->id, $user->id);
        }

        return back()->with('success', __('All messages marked as read'));
    }

    /**
     * Star/unstar message.
     */
    public function toggleStar(Message $message)
    {
        if ($message->receiver_id === Auth::id() || $message->sender_id === Auth::id()) {
            $message->toggleStar();
        }

        return back();
    }

    /**
     * Archive conversation.
     */
    public function archiveConversation(int $conversationId)
    {
        $this->messageService->archiveConversation($conversationId, Auth::id());

        return back()->with('success', __('Conversation archived'));
    }

    /**
     * Unarchive conversation.
     */
    public function unarchiveConversation(int $conversationId)
    {
        $this->messageService->unarchiveConversation($conversationId, Auth::id());

        return back()->with('success', __('Conversation unarchived'));
    }

    /**
     * Delete message.
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id === Auth::id() ||
            $message->receiver_id === Auth::id() ||
            $message->conversation?->hasParticipant(Auth::id())) {

            $this->messageService->deleteMessage($message->id, Auth::id());
            return back()->with('success', __('Message deleted'));
        }

        abort(403);
    }

    /**
     * Add reaction to message.
     */
    public function addReaction(Request $request, Message $message)
    {
        $request->validate([
            'reaction_type' => 'required|string|max:10',
        ]);

        try {
            $this->messageService->addReaction($message->id, $request->reaction_type);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove reaction from message.
     */
    public function removeReaction(Request $request, Message $message)
    {
        $request->validate([
            'reaction_type' => 'required|string|max:10',
        ]);

        try {
            $this->messageService->removeReaction($message->id, $request->reaction_type);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Pin a message.
     */
    public function pinMessage(Message $message)
    {
        try {
            $this->messageService->pinMessage($message->id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Unpin a message.
     */
    public function unpinMessage(Message $message)
    {
        try {
            $this->messageService->unpinMessage($message->id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Start typing in a conversation.
     */
    public function startTyping(Request $request, Conversation $conversation)
    {
        // Check if user has access to this conversation
        if (!$conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        $this->messageService->setTypingStatus($conversation->id, auth()->id(), true);

        // Broadcast typing event
        broadcast(new \App\Events\UserTyping($conversation->id, auth()->user(), true));

        return response()->json(['success' => true]);
    }

    /**
     * Stop typing in a conversation.
     */
    public function stopTyping(Request $request, Conversation $conversation)
    {
        // Check if user has access to this conversation
        if (!$conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        $this->messageService->setTypingStatus($conversation->id, auth()->id(), false);

        // Broadcast typing event
        broadcast(new \App\Events\UserTyping($conversation->id, auth()->user(), false));

        return response()->json(['success' => true]);
    }

    /**
     * Search messages.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'type' => 'nullable|string',
            'priority' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $results = $this->messageService->searchMessages(Auth::id(), $request->query, [
            'type' => $request->type,
            'priority' => $request->priority,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ]);

        return view('pages.messages.search', compact('results'));
    }

    /**
     * Search messages within a conversation.
     */
    public function searchConversation(Request $request, Conversation $conversation)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
        ]);

        // Check if user has access to this conversation
        if (!$conversation->hasParticipant(auth()->id())) {
            abort(403);
        }

        $results = $this->messageService->searchMessagesInConversation(
            $conversation->id,
            $request->query
        );

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    /**
     * Get message templates via AJAX.
     */
    public function getTemplates()
    {
        $templates = MessageTemplate::getAvailableForUser(Auth::id());

        return response()->json([
            'success' => true,
            'templates' => $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'category' => $template->category,
                    'variables' => $template->formatted_variables,
                ];
            }),
        ]);
    }

    /**
     * API endpoint for real-time messaging.
     */
    public function apiSendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'receiver_id' => 'required_without:conversation_id|exists:users,id',
            'content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        try {
            if ($request->conversation_id) {
                $message = $this->messageService->sendMessage(
                    $request->conversation_id,
                    $request->content,
                    ['attachments' => $request->file('attachments')]
                );
            } else {
                $message = $this->messageService->sendDirectMessage(
                    $request->receiver_id,
                    $request->content,
                    ['attachments' => $request->file('attachments')]
                );
            }

            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'messageAttachments']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:messages,id',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Check if user has access to this conversation
        if (!$conversation->hasParticipant(auth()->id())) {
            abort(403);
        }

        // Update online status
        auth()->user()->updateLastActivity();

        try {
            $message = $this->messageService->sendMessage(
                $conversation->id,
                $request->content,
                [
                    'parent_id' => $request->parent_id,
                    'attachments' => $request->file('attachments', []),
                ]
            );

            return redirect()->back()->with('success', __('Message sent successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => __('Failed to send message: ') . $e->getMessage()]);
        }
    }
}
