<?php
/**
 * Page: Student Appeal Details
 *
 * Purpose: Display detailed information about a specific grade appeal.
 * Shows appeal description, justification, attachments, teacher response, admin notes, and allows editing/withdrawing.
 *
 * Route: student.appeals.show (GET)
 *
 * Controller: App\Http\Controllers\Student\GradeAppealController@show
 *
 * Components on this page:
 * - Back navigation link
 * - Appeal information card (subject, description, justification, attachments)
 * - Teacher response section (if available)
 * - Admin notes section (if escalated)
 * - Sidebar with appeal details (ID, course, assessment, grades, dates)
 * - Action buttons for edit and withdraw (for pending/under review appeals)
 *
 * Required Data variables:
 * - $appeal: GradeAppeal model instance with relationships
 *
 * Dependencies:
 * - Routes: student.appeals.index, student.appeals.edit, student.appeals.withdraw
 * - Helpers: __(), route(), asset()
 * - Relationships: GradeAppeal->enrollment->offering->course, GradeAppeal->assessment, GradeAppeal->grade->assessment, GradeAppeal->reviewer
 * - Methods: GradeAppeal->isPending(), GradeAppeal->isUnderReview(), GradeAppeal->isEscalated()
 * - Flux UI components: flux:icon
 *
 * @package App\Views\Pages\Student\Appeals
 */
?>
<x-slot name="title">{{ __('lms.appeal_details') }}</x-slot>

<div class="mb-6">
    <div class="flex items-center mb-4">
        <a href="{{ route('student.appeals.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.appeal_details') }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
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
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.description') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.justification') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->student_justification }}</p>
                    </div>

                    @if($appeal->attachments && count($appeal->attachments) > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">{{ __('lms.attachments') }}</h3>
                            <div class="space-y-2">
                                @foreach($appeal->attachments as $attachment)
                                    <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <flux:icon name="document-text" class="w-5 h-5 text-gray-400 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment['name'] }}</span>
                                        <a href="{{ asset('storage/' . ltrim($attachment['path'], '/')) }}" target="_blank" class="ml-auto text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <flux:icon name="arrow-down-tray" class="w-5 h-5" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Teacher Response -->
            @if($appeal->teacher_response)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.teacher_response') }}</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->teacher_response }}</p>
                    </div>
                    @if($appeal->reviewer)
                        <div class="mt-4 flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <span>{{ __('lms.responded_by') }}: {{ $appeal->reviewer?->name ?? '-' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $appeal->reviewed_at?->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Admin Notes (if escalated) -->
            @if($appeal->admin_notes && $appeal->isEscalated())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.admin_notes') }}</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $appeal->admin_notes }}</p>
                    </div>
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
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.appeal_id') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">#{{ $appeal->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.course') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">
                            {{ $appeal->enrollment?->offering?->course?->name ?? $appeal->assessment?->courseOffering?->course?->name ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.assessment') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">
                            {{ $appeal->assessment?->title ?? $appeal->grade?->assessment?->title ?? '-' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.current_grade') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.requested_grade') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->requested_grade ?? '-' }}%</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.submitted') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($appeal->reviewed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('lms.reviewed') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $appeal->reviewed_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            @if($appeal->isPending() || $appeal->isUnderReview())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.actions') }}</h2>
                    <div class="space-y-3">
                        @if($appeal->isPending())
                            <a href="{{ route('student.appeals.edit', $appeal) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                <flux:icon name="pencil" class="w-5 h-5 mr-2" />
                                {{ __('lms.edit_appeal') }}
                            </a>
                        @endif
                        <form action="{{ route('student.appeals.withdraw', $appeal) }}" method="POST" onsubmit="return confirm('{{ __('lms.confirm_withdraw_appeal') }}')">
                            @csrf
                            @method('DELETE')
                            <x-button.submit variant="danger" loading-text="{{ __('Withdrawing...') }}" class="w-full inline-flex justify-center items-center">
                                <flux:icon name="x-mark" class="w-5 h-5 mr-2" />
                                {{ __('lms.withdraw_appeal') }}
                            </x-button.submit>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
