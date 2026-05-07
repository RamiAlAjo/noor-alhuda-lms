{{-- ==========================================
     NOOR LMS - Enhanced Sidebar Navigation
     Improved design, accessibility, and UX
     ========================================== --}}

<flux:sidebar
    sticky
    collapsible="mobile"
    class="
        border-e border-zinc-200/80
        bg-gradient-to-b from-zinc-50 to-white
        dark:border-zinc-700/50
        dark:from-zinc-900
        dark:to-zinc-950
        z-50
        transition-all
        duration-300
    "
    role="navigation"
    aria-label="{{ __('Main navigation sidebar') }}"
>
    {{-- Sidebar Header with Logo --}}
    <flux:sidebar.header class="px-4 py-3">
        <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate aria-label="{{ __('Go to dashboard') }}" />
        <flux:sidebar.collapse
            class="lg:hidden"
            aria-label="{{ __('Close sidebar') }}"
        />
    </flux:sidebar.header>

    {{-- Navigation Content --}}
    <flux:sidebar.nav
        role="navigation"
        aria-label="{{ __('Primary navigation') }}"
        class="overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-600"
    >
        {{-- Platform Section --}}
        <flux:sidebar.group :heading="__('Platform')" class="px-2">
            <flux:sidebar.item
                icon="home"
                :href="route('dashboard')"
                :current="request()->routeIs('dashboard')"
                wire:navigate
                class="rounded-lg transition-colors"
            >
                {{ __('Dashboard') }}
            </flux:sidebar.item>
        </flux:sidebar.group>

        {{-- Section Divider --}}
        <div class="mx-4 my-3 border-t border-zinc-200 dark:border-zinc-700/50"></div>

        {{-- Admin Specific Links --}}
        @role('admin')
        <flux:sidebar.group :heading="__('Administration')" class="px-2" aria-label="{{ __('Administration section') }}">
            <flux:sidebar.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                {{ __('Users') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="academic-cap" :href="route('admin.academic.index')" :current="request()->routeIs('admin.academic.index')" wire:navigate>
                {{ __('Academic Overview') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="calendar" :href="route('admin.academic.years')" :current="request()->routeIs('admin.academic.years')" wire:navigate>
                {{ __('Academic Years') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="building-office-2" :href="route('admin.academic.faculties')" :current="request()->routeIs('admin.academic.faculties')" wire:navigate>
                {{ __('Faculties') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="building-office" :href="route('admin.academic.departments')" :current="request()->routeIs('admin.academic.departments')" wire:navigate>
                {{ __('Departments') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="academic-cap" :href="route('admin.academic.majors')" :current="request()->routeIs('admin.academic.majors')" wire:navigate>
                {{ __('Majors') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open" :href="route('admin.academic.offerings')" :current="request()->routeIs('admin.academic.offerings')" wire:navigate>
                {{ __('Course Offerings') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open" :href="route('admin.courses.index')" :current="request()->routeIs('admin.courses.*')" wire:navigate>
                {{ __('Courses') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="user-plus" :href="route('admin.enrollments.index')" :current="request()->routeIs('admin.enrollments.*')" wire:navigate>
                {{ __('Enrollment') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="currency-dollar" :href="route('admin.fees.index')" :current="request()->routeIs('admin.fees.*') || request()->routeIs('admin.payments.*')" wire:navigate>
                {{ __('Fees & Payments') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chart-bar" :href="route('admin.reports.dashboard')" :current="request()->routeIs('admin.reports.*')" wire:navigate>
                {{ __('Reports') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="megaphone" :href="route('admin.announcements.index')" :current="request()->routeIs('admin.announcements.*')" wire:navigate>
                {{ __('Announcements') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="heart" :href="route('admin.medical.index')" :current="request()->routeIs('admin.medical.*')" wire:navigate>
                {{ __('Medical Records') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="cpu-chip" :href="route('admin.capacity.index')" :current="request()->routeIs('admin.capacity.*')" wire:navigate>
                {{ __('AI Capacity Management') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings.index')" :current="request()->routeIs('admin.settings.*')" wire:navigate>
                {{ __('Settings') }}
            </flux:sidebar.item>
        </flux:sidebar.group>
        @endrole

        {{-- Teacher Specific Links --}}
        @role('teacher')
        <flux:sidebar.group :heading="__('Teaching')" class="px-2" aria-label="{{ __('Teaching section') }}">
            <flux:sidebar.item icon="home" :href="route('teacher.dashboard')" :current="request()->routeIs('teacher.dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open" :href="route('teacher.courses.index')" :current="request()->routeIs('teacher.courses.*')" wire:navigate>
                {{ __('My Courses') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="question-mark-circle" :href="route('teacher.quizzes.all')" :current="request()->routeIs('teacher.quizzes.*')" wire:navigate>
                {{ __('Quizzes') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('teacher.discussions.index')" :current="request()->routeIs('teacher.discussions.*')" wire:navigate>
                {{ __('Discussions') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="academic-cap" :href="route('teacher.appeals.index')" :current="request()->routeIs('teacher.appeals.*')" wire:navigate>
                {{ __('Grade Appeals') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="document" :href="route('teacher.excused-absences.index')" :current="request()->routeIs('teacher.excused-absences.*')" wire:navigate>
                {{ __('Excused Absences') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="heart" :href="route('teacher.accommodations.index')" :current="request()->routeIs('teacher.accommodations.*')" wire:navigate>
                {{ __('Accommodations') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="document-chart-bar" :href="route('teacher.reports.index')" :current="request()->routeIs('teacher.reports.*')" wire:navigate>
                {{ __('Reports') }}
            </flux:sidebar.item>
        </flux:sidebar.group>
        @endrole

        {{-- Student Specific Links --}}
        @role('student')
        <flux:sidebar.group :heading="__('Learning')" class="px-2" aria-label="{{ __('Learning section') }}">
            <flux:sidebar.item icon="book-open" :href="route('student.courses.index')" :current="request()->routeIs('student.courses.index') || request()->routeIs('student.courses.show')" wire:navigate>
                {{ __('My Courses') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="magnifying-glass" :href="route('student.courses.browse')" :current="request()->routeIs('student.courses.browse')" wire:navigate>
                {{ __('Browse Courses') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="clipboard-document-list" :href="route('student.quizzes.index')" :current="request()->routeIs('student.quizzes.*')" wire:navigate>
                {{ __('Quizzes') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('student.discussions.index')" :current="request()->routeIs('student.discussions.*')" wire:navigate>
                {{ __('Discussions') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chart-bar" :href="route('student.grades')" :current="request()->routeIs('student.grades*')" wire:navigate>
                {{ __('Grades') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="document-text" :href="route('student.transcript.index')" :current="request()->routeIs('student.transcript.*')" wire:navigate>
                {{ __('Transcript') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="currency-dollar" :href="route('student.payments.index')" :current="request()->routeIs('student.payments.*') || request()->routeIs('student.fees.*')" wire:navigate>
                {{ __('Payments') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="heart" :href="route('student.medical.profile')" :current="request()->routeIs('student.medical.*')" wire:navigate>
                {{ __('Medical Record') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="calendar-days" :href="route('student.medical-leaves.index')" :current="request()->routeIs('student.medical-leaves.*')" wire:navigate>
                {{ __('Medical Leaves') }}
            </flux:sidebar.item>
        </flux:sidebar.group>
        @endrole

        {{-- Section Divider --}}
        <div class="mx-4 my-3 border-t border-zinc-200 dark:border-zinc-700/50"></div>

        {{-- Productivity Tools --}}
        <flux:sidebar.group :heading="__('Productivity')" class="px-2">
            <flux:sidebar.item icon="calendar-days" :href="route('calendar.index')" :current="request()->routeIs('calendar.*')" wire:navigate>
                {{ __('Calendar') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="clipboard-document-check" :href="route('tasks.index')" :current="request()->routeIs('tasks.*')" wire:navigate>
                {{ __('Tasks') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="bell-alert" :href="route('reminders.index')" :current="request()->routeIs('reminders.*')" wire:navigate>
                {{ __('Reminders') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="document-text" :href="route('notes.index')" :current="request()->routeIs('notes.*')" wire:navigate>
                {{ __('Notes') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chat-bubble-left-right" :href="route('messages.index')" :current="request()->routeIs('messages.*')" wire:navigate>
                {{ __('Messages') }}
            </flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>

    {{-- Spacer pushes user menu to bottom --}}
    <flux:spacer />

    {{-- Desktop User Menu --}}
    <div class="border-t border-zinc-200 dark:border-zinc-700/50 p-3">
        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </div>
</flux:sidebar>

{{-- Mobile Header with User Menu --}}
<flux:header
    class="
        lg:hidden
        border-b border-zinc-200 dark:border-zinc-700/50
        bg-white/80 dark:bg-zinc-900/80
        backdrop-blur-md
        sticky
        top-0
        z-40
    "
    role="banner"
>
    <flux:sidebar.toggle
        class="lg:hidden"
        icon="bars-3"
        inset="left"
        aria-label="{{ __('Open sidebar menu') }}"
    />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
        <flux:profile
            :initials="auth()->user()->initials()"
            icon-trailing="chevron-down"
            aria-haspopup="true"
            aria-label="{{ __('User menu') }}"
        />

        <flux:menu role="menu" aria-label="{{ __('User account menu') }}">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal" role="presentation">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            aria-hidden="true"
                        />

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator role="separator" />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate role="menuitem">
                    {{ __('Settings') }}
                </flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator role="separator" />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-left-end-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                    role="menuitem"
                >
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
