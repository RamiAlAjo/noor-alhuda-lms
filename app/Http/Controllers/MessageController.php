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
        if (! $conversation->hasParticipant($user->id)) {
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
        if ($message->receiver_id === Auth::id() && ! $message->is_read) {
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
        $studentIds = $request->get('students') ? explode(',', $request->get('students')) : null;

        $recipient = null;
        $recipients = collect();
        $conversation = null;
        $templates = MessageTemplate::getAvailableForUser(Auth::id());

        if ($userId) {
            $recipient = \App\Models\User::findOrFail($userId);
        }

        if ($studentIds) {
            $recipients = \App\Models\User::whereIn('id', $studentIds)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'student');
                })
                ->get();
        }

        if ($conversationId) {
            $conversation = Conversation::findOrFail($conversationId);
            // Check if user is participant
            if (! $conversation->hasParticipant(Auth::id())) {
                abort(403);
            }
        }

        return view('pages.messages.create', compact('recipient', 'recipients', 'conversation', 'templates'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'message' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'receiver_id' => 'nullable|exists:users,id',
            'selected_students' => 'nullable|string',
            'conversation_id' => 'nullable|exists:conversations,id',
            'template_id' => 'nullable|exists:message_templates,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:scheduled_at',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        // Handle both 'content' and 'message' fields for backward compatibility
        $messageContent = $request->content ?: $request->message;

        try {
            $user = Auth::user();

            // Handle template-based messages
            if ($request->template_id) {
                $template = MessageTemplate::findOrFail($request->template_id);
                if (! $template->is_public && $template->created_by !== $user->id) {
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
                        $messageContent,
                        $options
                    );
                } elseif ($request->filled('selected_students')) {
                    // Create group conversation with selected students
                    $studentIds = explode(',', $request->selected_students);
                    $participantIds = array_merge([$user->id], $studentIds);

                    $conversation = $this->messageService->createConversation(
                        $participantIds,
                        $request->subject ?: __('Group Message'),
                        $user->id
                    );

                    $message = $this->messageService->sendMessage(
                        $conversation->id,
                        $messageContent,
                        $options
                    );
                } else {
                    // Create new direct message
                    $message = $this->messageService->sendDirectMessage(
                        $request->receiver_id,
                        $messageContent,
                        $options
                    );
                }
            }

            $message = __('Message sent successfully!');
            if ($message->scheduled_at) {
                $message .= ' '.__('It will be sent at').' '.$message->scheduled_at->format('M j, Y \a\t g:i A');
            }

            return redirect()->route('messages.conversation', $message->conversation_id ?? $message->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to send message: '.$e->getMessage());
        }
    }

    /**
     * Create a new conversation.
     */
    public function createConversation(Request $request)
    {

        $request->validate([
            'participant_ids' => 'required|array|min:2',
            'participant_ids.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $participantIds = $request->participant_ids;
            $participantIds = array_unique($participantIds);

            // Current user should already be included in the form
            // But ensure they're there just in case
            if (! in_array(Auth::id(), $participantIds)) {
                $participantIds[] = Auth::id();
            }

            $conversation = $this->messageService->createConversation($participantIds, [
                'title' => $request->title,
            ]);

            // Send initial message
            $message = $this->messageService->sendMessage($conversation->id, $request->message);

            return redirect()->route('messages.conversation', $conversation->id)
                ->with('success', __('Conversation created successfully!'));

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create conversation: '.$e->getMessage());
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
        if (! $conversation->hasParticipant(auth()->id())) {
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
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        $this->messageService->setTypingStatus($conversation->id, auth()->id(), false);

        // Broadcast typing event
        broadcast(new \App\Events\UserTyping($conversation->id, auth()->user(), false));

        return response()->json(['success' => true]);
    }

    /**
     * Get typing users for a conversation.
     */
    public function getTypingUsers(Request $request, Conversation $conversation)
    {
        // Check if user has access to this conversation
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        $typingUsers = $this->messageService->getTypingUsers($conversation->id);

        return response()->json([
            'success' => true,
            'typing_users' => $typingUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
        ]);
    }

    /**
     * Update conversation details.
     */
    public function updateConversation(Request $request, Conversation $conversation)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        // Check if user has access to this conversation
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        // Only allow updates for group conversations
        if (! $conversation->is_group) {
            return response()->json(['success' => false, 'error' => 'Cannot update direct conversations'], 400);
        }

        $conversation->update($request->only(['title']));

        return response()->json([
            'success' => true,
            'message' => 'Conversation updated successfully',
        ]);
    }

    /**
     * Add a participant to a group conversation.
     */
    public function addParticipant(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user has access to this conversation
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        // Only allow for group conversations
        if (! $conversation->is_group) {
            return response()->json(['success' => false, 'error' => 'Cannot add participants to direct conversations'], 400);
        }

        // Check if user is admin (for now, allow any participant to add members)
        $user = \App\Models\User::find($request->user_id);

        if ($conversation->hasParticipant($request->user_id)) {
            return response()->json(['success' => false, 'error' => 'User is already a participant'], 400);
        }

        $conversation->addParticipant($user);

        return response()->json([
            'success' => true,
            'message' => 'Participant added successfully',
        ]);
    }

    /**
     * Remove a participant from a group conversation.
     */
    public function removeParticipant(Request $request, Conversation $conversation, \App\Models\User $user)
    {
        // Check if user has access to this conversation
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        // Only allow for group conversations
        if (! $conversation->is_group) {
            return response()->json(['success' => false, 'error' => 'Cannot remove participants from direct conversations'], 400);
        }

        // Check if user is admin or removing themselves
        $isAdmin = $conversation->participants()
            ->where('users.id', auth()->id())
            ->where('conversation_participants.is_admin', true)
            ->exists();

        if (! $isAdmin && $user->id !== auth()->id()) {
            return response()->json(['success' => false, 'error' => 'Only admins can remove other participants'], 403);
        }

        if (! $conversation->hasParticipant($user->id)) {
            return response()->json(['success' => false, 'error' => 'User is not a participant'], 400);
        }

        $conversation->removeParticipant($user);

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully',
        ]);
    }

    /**
     * Leave a conversation.
     */
    public function leaveConversation(Request $request, Conversation $conversation)
    {
        // Check if user has access to this conversation
        if (! $conversation->hasParticipant(auth()->id())) {
            return response()->json(['success' => false, 'error' => 'Access denied'], 403);
        }

        // Only allow for group conversations
        if (! $conversation->is_group) {
            return response()->json(['success' => false, 'error' => 'Cannot leave direct conversations'], 400);
        }

        $conversation->removeParticipant(auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the conversation',
        ]);
    }

    /**
     * Forward a message to another conversation.
     */
    public function forwardMessage(Request $request, Message $message)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'additional_message' => 'nullable|string|max:1000',
        ]);

        // Check if user has access to the original message
        if ($message->sender_id !== auth()->id() && ! $message->conversation->hasParticipant(auth()->id())) {
            abort(403, 'You do not have access to this message.');
        }

        // Check if user has access to the target conversation
        $targetConversation = Conversation::findOrFail($request->conversation_id);
        if (! $targetConversation->hasParticipant(auth()->id())) {
            abort(403, 'You do not have access to the target conversation.');
        }

        // Create the forwarded message content
        $forwardedContent = "Forwarded message:\n\"{$message->content}\"";
        if ($request->additional_message) {
            $forwardedContent .= "\n\n{$request->additional_message}";
        }

        // Send the forwarded message
        $forwardedMessage = $this->messageService->sendMessage(
            $targetConversation->id,
            $forwardedContent,
            [
                'message_type' => Message::TYPE_FORWARD,
                'metadata' => [
                    'original_message_id' => $message->id,
                    'original_conversation_id' => $message->conversation_id,
                    'original_sender_id' => $message->sender_id,
                    'forwarded_by' => auth()->id(),
                    'forwarded_at' => now(),
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Message forwarded successfully',
        ]);
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
        if (! $conversation->hasParticipant(auth()->id())) {
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
        if (! $conversation->hasParticipant(auth()->id())) {
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
            return redirect()->back()->withErrors(['error' => __('Failed to send message: ').$e->getMessage()]);
        }
    }
}
