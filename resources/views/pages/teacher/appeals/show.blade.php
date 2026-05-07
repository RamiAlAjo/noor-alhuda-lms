<?php
/**
 * Teacher Grade Appeals - Review Page
 *
 * Purpose: Review and respond to a specific grade appeal
 * Route: teacher.appeals.show (GET)
 * Controller: App\Http\Controllers\Teacher\GradeAppealController@show
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Student info: Name, ID, email
 * - Appeal details: Subject, description, justification, attachments
 * - Response forms: Mark as review, approve, reject, escalate
 * - Previous response display: If already reviewed
 * - Sidebar: Appeal details (ID, course, assessment, grades, dates)
 *
 * Required Data Variables:
 * - $appeal: GradeAppeal model instance
 *
 * Dependencies:
 * - Routes: teacher.appeals.index, teacher.appeals.review, teacher.appeals.approve, teacher.appeals.reject, teacher.appeals.escalate
 * - Models: GradeAppeal, User, Enrollment, CourseOffering, Course, Assessment, Grade
 * - Helpers: __(), route(), asset()
 * - Constants: GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW
 */
?>
<x-layouts::app :title="__('lms.review_appeal')">

<div class="mb-6">
    <div class="flex items-center mb-4">
        <a href="{{ route('teacher.appeals.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.review_appeal') }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.student_information') }}</h2>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg font-medium mr-4">
                        {{ $appeal->student->initials() }}
                    </div>
                    <div>
                        <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $appeal->student?->name ?? __('Unknown Student') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appeal->student->user_id }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appeal->student->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Appeal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $appeal->subject }}</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                            'under_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            'escalated' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                        ];
                    @endphp
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$appeal->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ __( 'lms.' . $appeal->status) }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.description') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.justification') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->student_justification }}</p>
                    </div>

                    @if($appeal->attachments && count($appeal->attachments) > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('lms.attachments') }}</h3>
                            <div class="space-y-2">
                                @foreach($appeal->attachments as $attachment)
                                    <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <flux:icon name="document-text" class="w-5 h-5 text-gray-400 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment['name'] }}</span>
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="ml-auto text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <flux:icon name="arrow-down-tray" class="w-5 h-5" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Response Form -->
            @if(in_array($appeal->status, [GradeAppeal::STATUS_PENDING, GradeAppeal::STATUS_UNDER_REVIEW]))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.respond_to_appeal') }}</h2>

                    <div class="space-y-4">
                        <!-- Mark as Under Review -->
                        @if($appeal->isPending())
                            <form action="{{ route('teacher.appeals.review', $appeal) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition mb-4">
                                    {{ __('lms.mark_as_under_review') }}
                                </button>
                            </form>
                        @endif

                        <!-- Approve Form -->
                        <form action="{{ route('teacher.appeals.approve', $appeal) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.response') }} <span class="text-red-500">*</span></label>
                                    <textarea name="teacher_response" rows="4"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('lms.approval_response_placeholder') }}" required></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.new_grade') }}</label>
                                    <input type="number" name="new_grade" step="0.01" min="0" max="100"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('lms.new_grade_placeholder') }}" value="{{ $appeal->requested_grade }}">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('lms.new_grade_help') }}</p>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    {{ __('lms.approve_appeal') }}
                                </button>
                            </div>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('teacher.appeals.reject', $appeal) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.rejection_reason') }} <span class="text-red-500">*</span></label>
                                    <textarea name="teacher_response" rows="4"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('lms.rejection_response_placeholder') }}" required></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    {{ __('lms.reject_appeal') }}
                                </button>
                            </div>
                        </form>

                        <!-- Escalate Form -->
                        <form action="{{ route('teacher.appeals.escalate', $appeal) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.escalation_reason') }} <span class="text-red-500">*</span></label>
                                    <textarea name="escalation_reason" rows="3"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('lms.escalation_reason_placeholder') }}" required></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                    {{ __('lms.escalate_to_admin') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Previous Response -->
            @if($appeal->teacher_response)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.previous_response') }}</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->teacher_response }}</p>
                    </div>
                    @if($appeal->reviewer)
                        <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ __('lms.responded_by') }}: {{ $appeal->reviewer?->name ?? '-' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $appeal->reviewed_at?->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Appeal Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.appeal_information') }}</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.appeal_id') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">#{{ $appeal->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.course') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">
                            {{ $appeal->enrollment?->offering?->course?->name ?? $appeal->assessment?->courseOffering?->course?->name ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.assessment') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">
                            {{ $appeal->assessment?->title ?? $appeal->grade?->assessment?->title ?? '-' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.current_grade') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.requested_grade') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->requested_grade ?? '-' }}%</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.submitted') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($appeal->reviewed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.reviewed') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->reviewed_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
</x-layouts::app>
