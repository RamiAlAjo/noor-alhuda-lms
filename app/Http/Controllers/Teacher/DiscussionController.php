<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DiscussionForum;
use App\Models\DiscussionReply;
use App\Models\DiscussionTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    /**
     * Display a listing of forums for teacher's courses.
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // Get teacher's course offerings
        $offeringIds = Auth::user()->taughtCourses()->pluck('course_offerings.id');

        $forums = DiscussionForum::with(['courseOffering.course', 'creator', 'topics'])
            ->withCount(['topics', 'replies'])
            ->whereIn('course_offering_id', $offeringIds)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('pages.teacher.discussions.index', compact('forums'));
    }

    /**
     * Show the form for creating a new forum.
     */
    public function createForum(Request $request)
    {
        $offeringId = $request->get('offering');

        // Get teacher's course offerings
        $offerings = Auth::user()->taughtCourses()->with('course')->get();

        return view('pages.teacher.discussions.create-forum', compact('offerings', 'offeringId'));
    }

    /**
     * Store a newly created forum.
     */
    public function storeForum(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Verify teacher teaches this course
        $hasAccess = Auth::user()->taughtCourses()
            ->where('course_offerings.id', $validated['course_offering_id'])
            ->exists();

        if (! $hasAccess) {
            return back()->with('error', __('lms.unauthorized_access'));
        }

        $forum = DiscussionForum::create([
            'course_offering_id' => $validated['course_offering_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('teacher.discussions.forum', $forum)
            ->with('success', __('lms.forum_created_successfully'));
    }

    /**
     * Display a specific forum with its topics.
     */
    public function showForum(Request $request, DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        $topics = DiscussionTopic::with(['user', 'lastReplyUser'])
            ->where('forum_id', $forum->id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->orderByDesc('is_pinned')
            ->orderByLastReply()
            ->paginate(15);

        return view('pages.teacher.discussions.forum', compact('forum', 'topics'));
    }

    /**
     * Update the forum.
     */
    public function updateForum(Request $request, DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $forum->update($validated);

        return back()->with('success', __('lms.forum_updated_successfully'));
    }

    /**
     * Delete the forum.
     */
    public function destroyForum(DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        $forum->delete();

        return redirect()->route('teacher.discussions.index')
            ->with('success', __('lms.forum_deleted_successfully'));
    }

    /**
     * Toggle forum lock status.
     */
    public function toggleForumLock(DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        $forum->is_locked ? $forum->unlock() : $forum->lock();

        return back()->with('success', $forum->is_locked ? __('lms.forum_unlocked') : __('lms.forum_locked'));
    }

    /**
     * Display a specific topic with its replies.
     */
    public function showTopic(DiscussionTopic $topic)
    {
        $this->authorizeTopicAccess($topic);

        $topic->incrementViews();
        $topic->load(['user', 'forum.courseOffering.course']);

        $replies = DiscussionReply::with(['user', 'children.user'])
            ->where('topic_id', $topic->id)
            ->root()
            ->orderByDesc('is_best_answer')
            ->orderBy('created_at')
            ->paginate(10);

        return view('pages.teacher.discussions.topic', compact('topic', 'replies'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function createTopic(DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        return view('pages.teacher.discussions.create-topic', compact('forum'));
    }

    /**
     * Store a newly created topic.
     */
    public function storeTopic(Request $request, DiscussionForum $forum)
    {
        $this->authorizeForumAccess($forum);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'is_announcement' => 'nullable|boolean',
        ]);

        $topic = DiscussionTopic::create([
            'forum_id' => $forum->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_announcement' => $validated['is_announcement'] ?? false,
            'last_reply_at' => now(),
            'last_reply_by' => Auth::id(),
        ]);

        return redirect()->route('teacher.discussions.topic', $topic)
            ->with('success', __('lms.topic_created_successfully'));
    }

    /**
     * Toggle topic lock status.
     */
    public function toggleTopicLock(DiscussionTopic $topic)
    {
        $this->authorizeTopicAccess($topic);

        $topic->is_locked ? $topic->unlock() : $topic->lock();

        return back()->with('success', $topic->is_locked ? __('lms.topic_unlocked') : __('lms.topic_locked'));
    }

    /**
     * Toggle topic pin status.
     */
    public function toggleTopicPin(DiscussionTopic $topic)
    {
        $this->authorizeTopicAccess($topic);

        $topic->is_pinned ? $topic->unpin() : $topic->pin();

        return back()->with('success', $topic->is_pinned ? __('lms.topic_unpinned') : __('lms.topic_pinned'));
    }

    /**
     * Delete a topic.
     */
    public function destroyTopic(DiscussionTopic $topic)
    {
        $this->authorizeTopicAccess($topic);

        $forum = $topic->forum;
        $topic->delete();

        return redirect()->route('teacher.discussions.forum', $forum)
            ->with('success', __('lms.topic_deleted_successfully'));
    }

    /**
     * Store a reply to a topic.
     */
    public function storeReply(Request $request, DiscussionTopic $topic)
    {
        $this->authorizeTopicAccess($topic);

        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:discussion_replies,id',
        ]);

        $reply = DiscussionReply::create([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $topic->updateLastReply(Auth::id());

        return redirect()->route('teacher.discussions.topic', $topic)
            ->with('success', __('lms.reply_posted_successfully'));
    }

    /**
     * Mark a reply as best answer.
     */
    public function markBestAnswer(DiscussionReply $reply)
    {
        $this->authorizeReplyAccess($reply);

        $reply->markAsBest(Auth::id());

        return back()->with('success', __('lms.marked_as_best_answer'));
    }

    /**
     * Unmark a reply as best answer.
     */
    public function unmarkBestAnswer(DiscussionReply $reply)
    {
        $this->authorizeReplyAccess($reply);

        $reply->unmarkAsBest();

        return back()->with('success', __('lms.unmarked_as_best_answer'));
    }

    /**
     * Delete a reply.
     */
    public function destroyReply(DiscussionReply $reply)
    {
        $this->authorizeReplyAccess($reply);

        $topic = $reply->topic;
        $reply->delete();

        return redirect()->route('teacher.discussions.topic', $topic)
            ->with('success', __('lms.reply_deleted_successfully'));
    }

    /**
     * Authorize forum access for teacher.
     */
    private function authorizeForumAccess(DiscussionForum $forum): void
    {
        $hasAccess = Auth::user()->taughtCourses()
            ->where('course_offerings.id', $forum->course_offering_id)
            ->exists();

        if (! $hasAccess) {
            abort(403, __('lms.unauthorized_access'));
        }
    }

    /**
     * Authorize topic access for teacher.
     */
    private function authorizeTopicAccess(DiscussionTopic $topic): void
    {
        $this->authorizeForumAccess($topic->forum);
    }

    /**
     * Authorize reply access for teacher.
     */
    private function authorizeReplyAccess(DiscussionReply $reply): void
    {
        $this->authorizeTopicAccess($reply->topic);
    }
}
