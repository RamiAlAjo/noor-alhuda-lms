<?php

use function Laravel\Folio\name;

name('student.feedback.edit');

$ratingCategories = [
    'content_quality' => __('Content Quality'),
    'instructor_knowledge' => __('Instructor Knowledge'),
    'instructor_communication' => __('Instructor Communication'),
    'course_organization' => __('Course Organization'),
    'materials_quality' => __('Materials Quality'),
    'workload_appropriateness' => __('Workload Appropriateness'),
];

?>

<?php $layout = 'layouts.app'; ?>
<?php if (app('auth')->check() && app('auth')->user()->role === 'admin'): ?>
<?php $layout = 'layouts.admin'; ?>
<?php elseif (app('auth')->check() && app('auth')->user()->role === 'teacher'): ?>
<?php $layout = 'layouts.app'; ?>
<?php endif; ?>

@extends($layout)

@section('subheader')
    <div class="flex items-center gap-4">
        <flux:button :href="route('student.feedback.index')" variant="ghost" size="sm">
            <flux:icon.chevron-left class="size-4" />
        </flux:button>
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            {{ __('Edit Course Feedback') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700 mb-4">
                <flux:heading :level="3" size="lg">
                    {{ __('Feedback for') }}: {{ $feedback->courseOffering?->course?->name ?? __('Course') }}
                </flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $feedback->courseOffering?->semester?->name }}</p>
            </div>

            <form method="POST" action="{{ route('student.feedback.update', $feedback) }}">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <!-- Overall Rating -->
                    <div>
                        <flux:label>{{ __('Overall Rating') }} *</flux:label>
                        <div class="flex gap-2 mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    class="star-rating-btn text-3xl {{ $i <= old('overall_rating', $feedback->overall_rating) ? 'text-yellow-500' : 'text-zinc-300 dark:text-zinc-600' }}"
                                    onclick="setRating({{ $i }})"
                                    @foreach($ratingCategories as $category => $label)
                                        data-{{ $category }}="{{ old($category, $feedback->$category ?? 0) }}"
                                    @endforeach
                                >
                                    ★
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="overall_rating" id="overall_rating" value="{{ old('overall_rating', $feedback->overall_rating) }}">
                        <flux:error name="overall_rating" />
                    </div>

                    <!-- Category Ratings -->
                    <div class="space-y-4">
                        <flux:heading :level="4" size="md">{{ __('Rate the Following') }}</flux:heading>

                        @foreach($ratingCategories as $category => $label)
                            <div>
                                <flux:label>{{ __($label) }}</flux:label>
                                <div class="flex gap-2 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            class="category-star-btn text-xl px-2 py-1 rounded {{ $i <= old($category, $feedback->$category ?? 0) ? 'bg-zinc-800 text-white dark:bg-white dark:text-zinc-800' : 'bg-zinc-100 text-zinc-400 dark:bg-zinc-700 dark:text-zinc-500' }}"
                                            onclick="setCategoryRating('{{ $category }}', {{ $i }})"
                                        >
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="{{ $category }}" id="{{ $category }}" value="{{ old($category, $feedback->$category ?? 0) }}">
                            </div>
                        @endforeach
                    </div>

                    <!-- Strengths -->
                    <div>
                        <flux:label>{{ __('What did you like most about this course?') }}</flux:label>
                        <flux:textarea name="strengths" rows="3" placeholder="{{ __('e.g., The hands-on projects, clear explanations, relevant content...') }}">{{ old('strengths', $feedback->strengths) }}</flux:textarea>
                    </div>

                    <!-- Improvements -->
                    <div>
                        <flux:label>{{ __('What areas could be improved?') }}</flux:label>
                        <flux:textarea name="improvements" rows="3" placeholder="{{ __('e.g., More examples, better textbook, faster feedback...') }}">{{ old('improvements', $feedback->improvements) }}</flux:textarea>
                    </div>

                    <!-- Additional Comments -->
                    <div>
                        <flux:label>{{ __('Additional Comments') }}</flux:label>
                        <flux:textarea name="additional_comments" rows="4" placeholder="{{ __('Any other thoughts or suggestions...') }}">{{ old('additional_comments', $feedback->additional_comments) }}</flux:textarea>
                    </div>

                    <!-- Anonymous -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" {{ old('is_anonymous', $feedback->is_anonymous) ? 'checked' : '' }} class="rounded border-zinc-300">
                        <flux:label for="is_anonymous" class="!mb-0">{{ __('Submit anonymously') }}</flux:label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <flux:button :href="route('student.feedback.index')" variant="secondary">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" name="save_draft" value="1" variant="secondary">
                        {{ __('Save as Draft') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Submit Feedback') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setRating(rating) {
            document.getElementById('overall_rating').value = rating;
            const buttons = document.querySelectorAll('.star-rating-btn');
            buttons.forEach((btn, index) => {
                if (index < rating) {
                    btn.classList.remove('text-zinc-300', 'dark:text-zinc-600');
                    btn.classList.add('text-yellow-500');
                } else {
                    btn.classList.remove('text-yellow-500');
                    btn.classList.add('text-zinc-300', 'dark:text-zinc-600');
                }
            });
        }

        function setCategoryRating(category, rating) {
            document.getElementById(category).value = rating;
            const container = document.getElementById(category).parentElement;
            const buttons = container.querySelectorAll('.category-star-btn');
            buttons.forEach((btn, index) => {
                if (index < rating) {
                    btn.classList.remove('bg-zinc-100', 'text-zinc-400', 'dark:bg-zinc-700', 'dark:text-zinc-500');
                    btn.classList.add('bg-zinc-800', 'text-white', 'dark:bg-white', 'dark:text-zinc-800');
                } else {
                    btn.classList.remove('bg-zinc-800', 'text-white', 'dark:bg-white', 'dark:text-zinc-800');
                    btn.classList.add('bg-zinc-100', 'text-zinc-400', 'dark:bg-zinc-700', 'dark:text-zinc-500');
                }
            });
        }
    </script>
