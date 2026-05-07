<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DiscussionForum;
use App\Models\DiscussionReply;
use App\Models\DiscussionTopic;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    /**
     * Display a listing of forums for student's enrolled courses.
     */
    public function index(Request $request)
    {
        $studentId = Auth::id();

        // Get student's enrolled course offerings
        $enrolledOfferingIds = Enrollment::where('student_id', $studentId)
            ->approved()
            ->pluck('course_offering_id');

        $forums = DiscussionForum::with(['courseOffering.course', 'creator'])
            ->withCount(['topics', 'replies'])
            ->whereIn('course_offering_id', $enrolledOfferingIds)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('pages.student.discussions.index', compact('forums'));
    }

    /**
     * Display forums for a specific course.
     */
    public function courseForums(Request $request, $offeringId)
    {
        // Verify student is enrolled
        $enrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_offering_id', $offeringId)
            ->approved()
            ->firstOrFail();

        $forums = DiscussionForum::with(['creator', 'topics'])
            ->withCount(['topics', 'replies'])
            ->where('course_offering_id', $offeringId)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(12);

        $courseOffering = $enrollment->offering;

        return view('pages.student.discussions.course-forums', compact('forums', 'courseOffering'));
    }

    /**
     * Display a specific forum with its topics.
     */
    public function showForum(Request $request, DiscussionForum $forum)
    {
        // Verify student has access
        $this->authorizeForumAccess($forum);

        $topics = DiscussionTopic::with(['user', 'lastReplyUser'])
            ->where('forum_id', $forum->id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->orderByDesc('is_pinned')
            ->orderByLastReply()
            ->paginate(15);

        return view('pages.student.discussions.forum', compact('forum', 'topics'));
    }

    /**
     * Display a specific topic with its replies.
     */
    public function showTopic(DiscussionTopic $topic)
    {
        // Verify student has access
        $this->authorizeTopicAccess($topic);

        // Increment view count
        $topic->incrementViews();

        $topic->load(['user', 'forum.courseOffering.course']);

        $replies = DiscussionReply::with(['user', 'children.user'])
            ->where('topic_id', $topic->id)
            ->root()
            ->orderByDesc('is_best_answer')
            ->orderBy('created_at')
            ->paginate(10);

        return view('pages.student.discussions.topic', compact('topic', 'replies'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function createTopic(DiscussionForum $forum)
    {
        // Verify student has access and forum is not locked
        $this->authorizeForumAccess($forum);

        if ($forum->is_locked) {
            return back()->with('error', __('lms.forum_is_locked'));
        }

        return view('pages.student.discussions.create-topic', compact('forum'));
    }

    /**
     * Store a newly created topic.
     */
    public function storeTopic(Request $request, DiscussionForum $forum)
    {
        // Verify student has access and forum is not locked
        $this->authorizeForumAccess($forum);

        if ($forum->is_locked) {
            return back()->with('error', __('lms.forum_is_locked'));
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $topic = DiscussionTopic::create([
            'forum_id' => $forum->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'last_reply_at' => now(),
            'last_reply_by' => Auth::id(),
        ]);

        return redirect()->route('student.discussions.topic', $topic)
            ->with('success', __('lms.topic_created_successfully'));
    }

    /**
     * Store a reply to a topic.
     */
    public function storeReply(Request $request, DiscussionTopic $topic)
    {
        // Verify student has access and topic is not locked
        $this->authorizeTopicAccess($topic);

        if ($topic->isLocked()) {
            return back()->with('error', __('lms.topic_is_locked'));
        }

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

        // Update topic's last reply info
        $topic->updateLastReply(Auth::id());

        return redirect()->route('student.discussions.topic', $topic)
            ->with('success', __('lms.reply_posted_successfully'));
    }

    /**
     * Edit a reply.
     */
    public function editReply(DiscussionReply $reply)
    {
        // Verify ownership
        if ($reply->user_id !== Auth::id()) {
            abort(403);
        }

        $reply->load('topic.forum');

        return view('pages.student.discussions.edit-reply', compact('reply'));
    }

    /**
     * Update a reply.
     */
    public function updateReply(Request $request, DiscussionReply $reply)
    {
        // Verify ownership
        if ($reply->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $reply->update(['content' => $validated['content']]);

        return redirect()->route('student.discussions.topic', $reply->topic)
            ->with('success', __('lms.reply_updated_successfully'));
    }

    /**
     * Delete a reply.
     */
    public function destroyReply(DiscussionReply $reply)
    {
        // Verify ownership
        if ($reply->user_id !== Auth::id()) {
            abort(403);
        }

        $topic = $reply->topic;
        $reply->delete();

        return redirect()->route('student.discussions.topic', $topic)
            ->with('success', __('lms.reply_deleted_successfully'));
    }

    /**
     * Authorize forum access for student.
     */
    private function authorizeForumAccess(DiscussionForum $forum): void
    {
        $hasAccess = Enrollment::where('student_id', Auth::id())
            ->where('course_offering_id', $forum->course_offering_id)
            ->approved()
            ->exists();

        if (! $hasAccess) {
            abort(403, __('lms.unauthorized_access'));
        }
    }

    /**
     * Authorize topic access for student.
     */
    private function authorizeTopicAccess(DiscussionTopic $topic): void
    {
        $this->authorizeForumAccess($topic->forum);
    }
}
