<?php

use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\Admin\AcademicStandingController;
use App\Http\Controllers\Admin\AccommodationController as AdminAccommodationController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseFeedbackController as AdminCourseFeedbackController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\GradeAppealController as AdminGradeAppealController;
use App\Http\Controllers\Admin\MedicalController;
use App\Http\Controllers\Admin\MedicalLeaveController as AdminMedicalLeaveController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\CourseFeedbackController as StudentCourseFeedbackController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\DiscussionController as StudentDiscussionController;
use App\Http\Controllers\Student\ExcusedAbsenceController as StudentExcusedAbsenceController;
use App\Http\Controllers\Student\GradeAppealController as StudentGradeAppealController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\MedicalController as StudentMedicalController;
use App\Http\Controllers\Student\MedicalLeaveController as StudentMedicalLeaveController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\TranscriptController as StudentTranscriptController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Teacher\AccommodationController as TeacherAccommodationController;
use App\Http\Controllers\Teacher\AnnouncementController as TeacherAnnouncementController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\DiscussionController as TeacherDiscussionController;
use App\Http\Controllers\Teacher\ExcusedAbsenceController as TeacherExcusedAbsenceController;
use App\Http\Controllers\Teacher\GradeAppealController as TeacherGradeAppealController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ReportController as TeacherReportController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\UserSettingsController;
use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Test email route (remove in production)
// Route::get('/test-email', function () {
//     $user = User::where('email', 'ramialajo@outlook.com')->first();
//
//     if (!$user) {
//         $user = User::create([
//             'email' => 'ramialajo@outlook.com',
//             'password' => Hash::make('Test123!'),
//             'name' => 'Rami Test',
//             'user_id' => 'TEST-001'
//         ]);
//
//         $user->profile()->create([
//             'first_name' => 'Rami',
//             'last_name' => 'Alajo'
//         ]);
//
//         $user->assignRole('student');
//     }
//
//     try {
//         Mail::to('ramialajo@outlook.com')->send(new UserCredentials($user, 'Test123!'));
//         return 'Email sent successfully! Check your Laravel log file.';
//     } catch (\Exception $e) {
//         return 'Email error: ' . $e->getMessage();
//     }
// });

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Gradient switcher for login page
Route::post('/gradient/switch', [ThemeController::class, 'switchGradient'])->name('gradient.switch');

// Mobile App Page
Route::get('/mobile-app', function () {
    return view('pages.mobile-app');
})->name('mobile-app');

// Serve files from storage (fixes Windows symlink issue with PHP built-in server)
Route::get('/storage/{path}', function ($path) {
    // Check both possible locations for the file
    $fullPath = 'app/public/'.$path;

    // First try the exact path as stored
    if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($fullPath)) {
        // Try alternative path (without 'uploads/' prefix if present)
        $altPath = preg_replace('#^uploads/#', '', $path);
        $altFullPath = 'app/public/'.$altPath;
        if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($altFullPath)) {
            // Try without week number subdirectory (e.g., /materials/1/ -> /materials/)
            $altPath2 = preg_replace('#/\d+/#', '/', $altPath);
            $altFullPath2 = 'app/public/'.$altPath2;
            if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($altFullPath2)) {
                abort(404);
            }
            $fullPath = $altFullPath2;
        } else {
            $fullPath = $altFullPath;
        }
    }

    $storagePath = \Illuminate\Support\Facades\Storage::disk('local')->path($fullPath);

    return response()->file($storagePath);
})->where('path', '.*');

// Public routes for language and theme switching
Route::get('/language/{lang}', [UserSettingsController::class, 'switchLanguage'])->name('language.switch');
Route::post('/settings/theme', [UserSettingsController::class, 'switchTheme'])->name('settings.theme.switch');

// Accessibility settings - available to all users
Route::post('/settings/accessibility/toggle', [UserSettingsController::class, 'toggleAccessibility'])->name('settings.accessibility.toggle');
Route::post('/settings/accessibility/reset', [UserSettingsController::class, 'resetAccessibility'])->name('settings.accessibility.reset');

// Role-based dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // User Management - explicit routes must come before resource
        Route::get('/users/import', [UserController::class, 'import'])->name('admin.users.import');
        Route::post('/users/import', [UserController::class, 'processImport'])->name('admin.users.process-import');
        Route::get('/users/export', [UserController::class, 'export'])->name('admin.users.export');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('admin.users.deactivate');
        Route::post('/users/{user}/send-credentials', [UserController::class, 'sendCredentials'])->name('admin.users.send-credentials');
        Route::post('/users/bulk-activate', [UserController::class, 'bulkActivate'])->name('admin.users.bulk-activate');
        Route::post('/users/bulk-deactivate', [UserController::class, 'bulkDeactivate'])->name('admin.users.bulk-deactivate');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('admin.users.bulk-delete');
        Route::resource('users', UserController::class)->names('admin.users');

        // Academic Structure
        Route::get('/academic', [AcademicController::class, 'index'])->name('admin.academic.index');
        Route::get('/academic/offerings', [AcademicController::class, 'offerings'])->name('admin.academic.offerings');
        Route::post('/academic/offerings', [AcademicController::class, 'storeOffering'])->name('admin.academic.offerings.store');
        Route::put('/academic/offerings/{offering}', [AcademicController::class, 'updateOffering'])->name('admin.academic.offerings.update');
        Route::delete('/academic/offerings/{offering}', [AcademicController::class, 'destroyOffering'])->name('admin.academic.offerings.destroy');
        Route::get('/academic/years', [AcademicController::class, 'academicYears'])->name('admin.academic.years');
        Route::post('/academic/years', [AcademicController::class, 'storeAcademicYear'])->name('admin.academic.years.store');
        Route::put('/academic/years/{year}', [AcademicController::class, 'updateAcademicYear'])->name('admin.academic.years.update');
        Route::delete('/academic/years/{year}', [AcademicController::class, 'destroyAcademicYear'])->name('admin.academic.years.destroy');

        Route::post('/academic/semesters', [AcademicController::class, 'storeSemester'])->name('admin.academic.semesters.store');
        Route::put('/academic/semesters/{semester}', [AcademicController::class, 'updateSemester'])->name('admin.academic.semesters.update');
        Route::delete('/academic/semesters/{semester}', [AcademicController::class, 'destroySemester'])->name('admin.academic.semesters.destroy');

        Route::get('/academic/faculties', [AcademicController::class, 'faculties'])->name('admin.academic.faculties');
        Route::post('/academic/faculties', [AcademicController::class, 'storeFaculty'])->name('admin.academic.faculties.store');
        Route::put('/academic/faculties/{faculty}', [AcademicController::class, 'updateFaculty'])->name('admin.academic.faculties.update');
        Route::delete('/academic/faculties/{faculty}', [AcademicController::class, 'destroyFaculty'])->name('admin.academic.faculties.destroy');

        Route::get('/academic/departments', [AcademicController::class, 'departments'])->name('admin.academic.departments');

        Route::post('/academic/departments', [AcademicController::class, 'storeDepartment'])->name('admin.academic.departments.store');
        Route::put('/academic/departments/{department}', [AcademicController::class, 'updateDepartment'])->name('admin.academic.departments.update');
        Route::delete('/academic/departments/{department}', [AcademicController::class, 'destroyDepartment'])->name('admin.academic.departments.destroy');

        Route::get('/academic/majors', [AcademicController::class, 'majors'])->name('admin.academic.majors');

        Route::post('/academic/majors', [AcademicController::class, 'storeMajor'])->name('admin.academic.majors.store');
        Route::put('/academic/majors/{major}', [AcademicController::class, 'updateMajor'])->name('admin.academic.majors.update');
        Route::delete('/academic/majors/{major}', [AcademicController::class, 'destroyMajor'])->name('admin.academic.majors.destroy');

        // Course Management
        Route::resource('courses', AdminCourseController::class)->names('admin.courses');
        Route::get('/courses/{course}/sections/create', [AdminCourseController::class, 'createSection'])->name('admin.courses.sections.create');
        Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('admin.courses.sections.store');
        Route::delete('/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('admin.sections.destroy');

        // Enrollment Management
        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('admin.enrollments.index');
        Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('admin.enrollments.create');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('admin.enrollments.store');
        Route::get('/enrollments/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('admin.enrollments.edit');
        Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('admin.enrollments.update');
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('admin.enrollments.destroy');
        Route::get('/enrollments/requests', [EnrollmentController::class, 'requests'])->name('admin.enrollments.requests');
        Route::post('/enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve'])->name('admin.enrollments.approve');
        Route::post('/enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject'])->name('admin.enrollments.reject');
        Route::post('/enrollments/bulk-approve', [EnrollmentController::class, 'bulkApprove'])->name('admin.enrollments.bulk-approve');
        Route::post('/enrollments/bulk-reject', [EnrollmentController::class, 'bulkReject'])->name('admin.enrollments.bulk-reject');
        Route::get('/enrollments/bulk-create', [EnrollmentController::class, 'createBulk'])->name('admin.enrollments.bulk-create');
        Route::post('/enrollments/bulk', [EnrollmentController::class, 'storeBulk'])->name('admin.enrollments.store-bulk');
        Route::get('/enrollments/import-csv', [EnrollmentController::class, 'importCsv'])->name('admin.enrollments.import-csv');
        Route::post('/enrollments/import-csv', [EnrollmentController::class, 'processCsvImport'])->name('admin.enrollments.process-csv-import');
        Route::get('/enrollments/template', [EnrollmentController::class, 'downloadTemplate'])->name('admin.enrollments.template');
        Route::get('/enrollments/export', [EnrollmentController::class, 'export'])->name('admin.enrollments.export');

        // Fees & Payments
        Route::get('/fees', [FeeController::class, 'index'])->name('admin.fees.index');
        Route::post('/fees', [FeeController::class, 'store'])->name('admin.fees.store');
        Route::put('/fees/{fee}', [FeeController::class, 'update'])->name('admin.fees.update');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('admin.fees.destroy');
        Route::get('/fees/export', [FeeController::class, 'exportFees'])->name('admin.fees.export');
        Route::get('/fees/reports', [FeeController::class, 'reports'])->name('admin.fees.reports');
        Route::get('/fees/reports/export-summary', [FeeController::class, 'exportFinancialSummary'])->name('admin.fees.export-summary');
        Route::get('/payments', [FeeController::class, 'payments'])->name('admin.payments.index');
        Route::post('/payments/{payment}/approve', [FeeController::class, 'approvePayment'])->name('admin.payments.approve');
        Route::post('/payments/{payment}/reject', [FeeController::class, 'rejectPayment'])->name('admin.payments.reject');
        Route::post('/payments', [FeeController::class, 'storePayment'])->name('admin.payments.store');
        Route::put('/payments/{payment}', [FeeController::class, 'updatePayment'])->name('admin.payments.update');
        Route::delete('/payments/{payment}', [FeeController::class, 'deletePayment'])->name('admin.payments.destroy');
        Route::get('/payments/export', [FeeController::class, 'exportPayments'])->name('admin.payments.export');
        Route::get('/outstanding-balances/export', [FeeController::class, 'exportOutstandingBalances'])->name('admin.outstanding-balances.export');

        // Reports & Analytics
        Route::get('/reports', [ReportController::class, 'dashboard'])->name('admin.reports.dashboard');
        Route::get('/reports/enrollment', [ReportController::class, 'enrollmentStats'])->name('admin.reports.enrollment');
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('admin.reports.attendance');
        Route::get('/reports/gpa', [ReportController::class, 'gpa'])->name('admin.reports.gpa');
        Route::get('/reports/gpa/export', [ReportController::class, 'exportGpa'])->name('admin.reports.gpa.export');
        Route::get('/reports/custom', [ReportController::class, 'custom'])->name('admin.reports.custom');

        // Announcements
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('admin.announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
        Route::post('/announcements/{announcement}/pin', [AnnouncementController::class, 'togglePin'])->name('admin.announcements.pin');
        Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggleActive'])->name('admin.announcements.toggle');

        // Medical Records
        Route::get('/medical', [MedicalController::class, 'index'])->name('admin.medical.index');
        Route::get('/medical/{student}', [MedicalController::class, 'show'])->name('admin.medical.show');
        Route::put('/medical/{student}', [MedicalController::class, 'update'])->name('admin.medical.update');
        Route::delete('/medical/{medicalRecord}', [MedicalController::class, 'destroy'])->name('admin.medical.destroy');

        // Transcripts
        Route::get('/transcript/{student}', [StudentTranscriptController::class, 'adminView'])->name('admin.transcript.show');
        Route::get('/transcript/{student}/export-pdf', [StudentTranscriptController::class, 'adminExportPdf'])->name('admin.transcript.export-pdf');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [SettingController::class, 'updateSettings'])->name('admin.settings.update');
        Route::post('/settings/maintenance', [SettingController::class, 'toggleMaintenance'])->name('admin.settings.maintenance');
        Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('admin.settings.test-email');
        Route::post('/settings/clear-all-caches', [SettingController::class, 'clearAllCaches'])->name('admin.settings.clear-all-caches');
        Route::get('/settings/theme', [SettingController::class, 'theme'])->name('admin.settings.theme');
        Route::put('/settings/theme', [SettingController::class, 'updateTheme'])->name('admin.settings.theme.update');
        Route::get('/settings/logs', [SettingController::class, 'logs'])->name('admin.settings.logs');
        Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('admin.settings.clear-cache');
        Route::get('/settings/clear-cache', function () {
            return redirect()->route('admin.settings.index');
        });
        Route::get('/settings/system-info', [SettingController::class, 'systemInfo'])->name('admin.settings.system-info');
        Route::get('/settings/backups', [SettingController::class, 'backups'])->name('admin.settings.backups');
        Route::post('/settings/backup', [SettingController::class, 'createBackup'])->name('admin.settings.backup');

        // Bulk Notifications
        Route::get('/notifications/bulk', [\App\Http\Controllers\Admin\BulkNotificationController::class, 'index'])->name('admin.notifications.bulk');
        Route::post('/notifications/send', [\App\Http\Controllers\Admin\BulkNotificationController::class, 'send'])->name('admin.notifications.send');
        Route::post('/notifications/preview', [\App\Http\Controllers\Admin\BulkNotificationController::class, 'preview'])->name('admin.notifications.preview');
        Route::get('/notifications/analytics', [\App\Http\Controllers\Admin\BulkNotificationController::class, 'analytics'])->name('admin.notifications.analytics');

        // User Notification Settings
        Route::get('/settings/notifications', function () {
            return view('pages.settings.notifications');
        })->name('settings.notifications');
        Route::put('/settings/notifications', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'notification_email' => 'boolean',
                'notification_push' => 'boolean',
                'notification_grades' => 'boolean',
                'notification_enrollment' => 'boolean',
                'notification_payments' => 'boolean',
                'notification_announcements' => 'boolean',
                'notification_reminders' => 'boolean',
            ]);

            $user = auth()->user();
            if ($user->settings) {
                $user->settings->update($request->only([
                    'notification_email', 'notification_push', 'notification_grades',
                    'notification_enrollment', 'notification_payments', 'notification_announcements',
                    'notification_reminders'
                ]));
            }

            return back()->with('success', __('Notification preferences updated successfully.'));
        })->name('settings.notifications.update');
        Route::post('/settings/notifications/test', function (\Illuminate\Http\Request $request) {
            $type = $request->input('type', 'push');
            $user = auth()->user();

            switch ($type) {
                case 'push':
                    \App\Models\Notification::createForUser(
                        $user,
                        'system',
                        'Test Notification',
                        'This is a test push notification to verify your settings.',
                        route('dashboard')
                    );
                    break;
                case 'email':
                    \Mail::raw('This is a test email notification.', function ($message) use ($user) {
                        $message->to($user->email)->subject('Test Email Notification');
                    });
                    break;
                case 'sound':
                    // Sound test is handled by frontend
                    break;
            }

            return back()->with('success', ucfirst($type) . ' test completed.');
        })->name('settings.notifications.test');

        // User Notifications Page
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'userIndex'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.delete');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('admin.activity-logs.export');
        Route::get('/activity-logs/statistics', [ActivityLogController::class, 'statistics'])->name('admin.activity-logs.statistics');
        Route::get('/activity-logs/user/{user}', [ActivityLogController::class, 'userLogs'])->name('admin.activity-logs.user');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('admin.activity-logs.show');

        // Grade Appeals
        Route::get('/appeals', [AdminGradeAppealController::class, 'index'])->name('admin.appeals.index');
        Route::get('/appeals/escalated', [AdminGradeAppealController::class, 'escalated'])->name('admin.appeals.escalated');
        Route::get('/appeals/export', [AdminGradeAppealController::class, 'export'])->name('admin.appeals.export');
        Route::get('/appeals/{appeal}', [AdminGradeAppealController::class, 'show'])->name('admin.appeals.show');
        Route::put('/appeals/{appeal}/status', [AdminGradeAppealController::class, 'updateStatus'])->name('admin.appeals.update-status');
        Route::post('/appeals/{appeal}/approve', [AdminGradeAppealController::class, 'approve'])->name('admin.appeals.approve');
        Route::post('/appeals/{appeal}/reject', [AdminGradeAppealController::class, 'reject'])->name('admin.appeals.reject');

        // Medical Leaves
        Route::get('/medical-leaves', [AdminMedicalLeaveController::class, 'index'])->name('admin.medical-leaves.index');
        Route::get('/medical-leaves/export', [AdminMedicalLeaveController::class, 'export'])->name('admin.medical-leaves.export');
        Route::get('/medical-leaves/{medicalLeave}', [AdminMedicalLeaveController::class, 'show'])->name('admin.medical-leaves.show');
        Route::post('/medical-leaves/{medicalLeave}/approve', [AdminMedicalLeaveController::class, 'approve'])->name('admin.medical-leaves.approve');
        Route::post('/medical-leaves/{medicalLeave}/reject', [AdminMedicalLeaveController::class, 'reject'])->name('admin.medical-leaves.reject');

        // Academic Standings
        Route::get('/academic-standings', [AcademicStandingController::class, 'index'])->name('admin.academic-standings.index');
        Route::get('/academic-standings/create', [AcademicStandingController::class, 'create'])->name('admin.academic-standings.create');
        Route::post('/academic-standings', [AcademicStandingController::class, 'store'])->name('admin.academic-standings.store');
        Route::get('/academic-standings/{academicStanding}', [AcademicStandingController::class, 'show'])->name('admin.academic-standings.show');
        Route::get('/academic-standings/{academicStanding}/edit', [AcademicStandingController::class, 'edit'])->name('admin.academic-standings.edit');
        Route::put('/academic-standings/{academicStanding}', [AcademicStandingController::class, 'update'])->name('admin.academic-standings.update');
        Route::post('/academic-standings/{academicStanding}/deactivate', [AcademicStandingController::class, 'deactivate'])->name('admin.academic-standings.deactivate');
        Route::post('/academic-standings/calculate-all', [AcademicStandingController::class, 'calculateAll'])->name('admin.academic-standings.calculate-all');
        Route::get('/academic-standings/export', [AcademicStandingController::class, 'export'])->name('admin.academic-standings.export');

        // Course Feedback
        Route::get('/feedback', [AdminCourseFeedbackController::class, 'index'])->name('admin.feedback.index');
        Route::get('/feedback/export', [AdminCourseFeedbackController::class, 'export'])->name('admin.feedback.export');
        Route::get('/feedback/reports', [AdminCourseFeedbackController::class, 'reports'])->name('admin.feedback.reports');
        Route::get('/feedback/course/{courseOffering}', [AdminCourseFeedbackController::class, 'showCourse'])->name('admin.feedback.course');

        // Accommodations
        Route::get('/accommodations', [AdminAccommodationController::class, 'index'])->name('admin.accommodations.index');
        Route::post('/accommodations/types', [AdminAccommodationController::class, 'storeType'])->name('admin.accommodations.store-type');
        Route::put('/accommodations/types/{accommodationType}', [AdminAccommodationController::class, 'updateType'])->name('admin.accommodations.update-type');
        Route::delete('/accommodations/types/{accommodationType}', [AdminAccommodationController::class, 'destroyType'])->name('admin.accommodations.destroy-type');
        Route::get('/accommodations/create-student', [AdminAccommodationController::class, 'createStudentAccommodation'])->name('admin.accommodations.create-student');
        Route::post('/accommodations/student', [AdminAccommodationController::class, 'storeStudentAccommodation'])->name('admin.accommodations.store-student');
        Route::get('/accommodations/student/{studentAccommodation}', [AdminAccommodationController::class, 'showStudentAccommodation'])->name('admin.accommodations.show-student');
        Route::get('/accommodations/student/{studentAccommodation}/edit', [AdminAccommodationController::class, 'editStudentAccommodation'])->name('admin.accommodations.edit-student');
        Route::put('/accommodations/student/{studentAccommodation}', [AdminAccommodationController::class, 'updateStudentAccommodation'])->name('admin.accommodations.update-student');
        Route::delete('/accommodations/student/{studentAccommodation}', [AdminAccommodationController::class, 'destroyStudentAccommodation'])->name('admin.accommodations.destroy-student');
        Route::get('/accommodations/export', [AdminAccommodationController::class, 'export'])->name('admin.accommodations.export');
        Route::get('/accommodations/api/student/{student}', [AdminAccommodationController::class, 'getStudentAccommodations'])->name('admin.accommodations.api.student');

        // AI Capacity Management
        Route::get('/capacity', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'index'])->name('admin.capacity.index');
        Route::get('/capacity/analytics', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'analytics'])->name('admin.capacity.analytics');
        Route::get('/capacity/{offeringId}', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'show'])->name('admin.capacity.show');
        Route::post('/capacity/predict', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'predict'])->name('admin.capacity.predict');
        Route::post('/capacity/apply', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'applyRecommendation'])->name('admin.capacity.apply');
        Route::post('/capacity/batch', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'batchPredict'])->name('admin.capacity.batch');
        Route::get('/capacity/export', [\App\Http\Controllers\Admin\CapacityManagementController::class, 'export'])->name('admin.capacity.export');
    });

    // Teacher routes
    Route::middleware(['role:teacher'])->prefix('teacher')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');

        // Teacher Courses
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('teacher.courses.index');
        Route::get('/courses/{section}', [TeacherCourseController::class, 'show'])->name('teacher.courses.show');
        Route::get('/courses/{section}/students', [TeacherCourseController::class, 'students'])->name('teacher.courses.students');
        Route::get('/courses/{section}/attendance', [TeacherCourseController::class, 'attendance'])->name('teacher.courses.attendance');
        Route::post('/courses/{section}/attendance', [TeacherCourseController::class, 'storeAttendance'])->name('teacher.courses.attendance.store');
        Route::get('/courses/{section}/materials', [TeacherCourseController::class, 'materials'])->name('teacher.courses.materials');
        Route::post('/courses/{section}/materials', [TeacherCourseController::class, 'storeMaterial'])->name('teacher.courses.materials.store');
        Route::delete('/materials/{material}', [TeacherCourseController::class, 'destroyMaterial'])->name('teacher.materials.destroy');
        Route::get('/courses/{section}/assessments', [TeacherCourseController::class, 'assessments'])->name('teacher.courses.assessments');
        Route::post('/courses/{section}/assessments', [TeacherCourseController::class, 'storeAssessment'])->name('teacher.courses.assessments.store');
        Route::get('/courses/{section}/assessments/{assessment}/questions', [TeacherCourseController::class, 'questions'])->name('teacher.courses.assessments.questions');
        Route::post('/courses/{section}/assessments/{assessment}/questions', [TeacherCourseController::class, 'storeQuestion'])->name('teacher.courses.assessments.questions.store');
        Route::delete('/questions/{question}', [TeacherCourseController::class, 'destroyQuestion'])->name('teacher.questions.destroy');
        Route::get('/courses/{section}/assessments/{assessment}/grade/{studentGrade}', [TeacherCourseController::class, 'gradeStudent'])->name('teacher.courses.assessments.grade');
        Route::post('/courses/{section}/assessments/{assessment}/grade/{studentGrade}', [TeacherCourseController::class, 'storeGrade'])->name('teacher.courses.assessments.grade.store');
        Route::get('/courses/{section}/assessments/{assessment}/preview', [TeacherCourseController::class, 'previewAssessment'])->name('teacher.courses.assessments.preview');
        Route::get('/courses/{section}/grades', [TeacherCourseController::class, 'grades'])->name('teacher.courses.grades');
        Route::get('/courses/{section}/grades/{assessment}', [TeacherCourseController::class, 'viewGrades'])->name('teacher.courses.grades.view');
        Route::get('/courses/{section}/announcements', [TeacherAnnouncementController::class, 'index'])->name('teacher.courses.announcements');
        Route::post('/courses/{section}/announcements', [TeacherAnnouncementController::class, 'store'])->name('teacher.courses.announcements.store');

        // Teacher Quizzes
        Route::get('/quizzes/all', [TeacherQuizController::class, 'allQuizzes'])->name('teacher.quizzes.all');
        Route::get('/offerings/{offering}/quizzes', [TeacherQuizController::class, 'index'])->name('teacher.quizzes.index');
        Route::get('/offerings/{offering}/quizzes/create', [TeacherQuizController::class, 'create'])->name('teacher.quizzes.create');
        Route::post('/offerings/{offering}/quizzes', [TeacherQuizController::class, 'store'])->name('teacher.quizzes.store');
        Route::get('/offerings/{offering}/quizzes/{quiz}/edit', [TeacherQuizController::class, 'edit'])->name('teacher.quizzes.edit');
        Route::put('/offerings/{offering}/quizzes/{quiz}', [TeacherQuizController::class, 'update'])->name('teacher.quizzes.update');
        Route::delete('/offerings/{offering}/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('teacher.quizzes.destroy');
        Route::get('/offerings/{offering}/quizzes/{quiz}/questions', [TeacherQuizController::class, 'questions'])->name('teacher.quizzes.questions');
        Route::post('/offerings/{offering}/quizzes/{quiz}/questions', [TeacherQuizController::class, 'storeQuestion'])->name('teacher.quizzes.questions.store');
        Route::put('/offerings/{offering}/quizzes/{quiz}/questions/{question}', [TeacherQuizController::class, 'updateQuestion'])->name('teacher.quizzes.questions.update');
        Route::delete('/offerings/{offering}/quizzes/{quiz}/questions/{question}', [TeacherQuizController::class, 'destroyQuestion'])->name('teacher.quizzes.questions.destroy');
        Route::post('/offerings/{offering}/quizzes/{quiz}/reorder', [TeacherQuizController::class, 'reorderQuestions'])->name('teacher.quizzes.reorder');
        Route::get('/offerings/{offering}/quizzes/{quiz}/analytics', [TeacherQuizController::class, 'analytics'])->name('teacher.quizzes.analytics');
        Route::get('/offerings/{offering}/quizzes/{quiz}/attempts/{attempt}', [TeacherQuizController::class, 'showAttempt'])->name('teacher.quizzes.attempts.show');
        Route::post('/offerings/{offering}/quizzes/{quiz}/attempts/{attempt}/grade', [TeacherQuizController::class, 'gradeAttempt'])->name('teacher.quizzes.attempts.grade');
        Route::post('/offerings/{offering}/quizzes/{quiz}/toggle-publish', [TeacherQuizController::class, 'togglePublish'])->name('teacher.quizzes.toggle-publish');
        Route::get('/offerings/{offering}/quizzes/{quiz}/preview', [TeacherQuizController::class, 'preview'])->name('teacher.quizzes.preview');
        Route::post('/offerings/{offering}/quizzes/{quiz}/duplicate', [TeacherQuizController::class, 'duplicate'])->name('teacher.quizzes.duplicate');

        // Teacher Grade Appeals
        Route::get('/appeals', [TeacherGradeAppealController::class, 'index'])->name('teacher.appeals.index');
        Route::get('/appeals/{appeal}', [TeacherGradeAppealController::class, 'show'])->name('teacher.appeals.show');
        Route::post('/appeals/{appeal}/review', [TeacherGradeAppealController::class, 'review'])->name('teacher.appeals.review');
        Route::post('/appeals/{appeal}/approve', [TeacherGradeAppealController::class, 'approve'])->name('teacher.appeals.approve');
        Route::post('/appeals/{appeal}/reject', [TeacherGradeAppealController::class, 'reject'])->name('teacher.appeals.reject');
        Route::post('/appeals/{appeal}/escalate', [TeacherGradeAppealController::class, 'escalate'])->name('teacher.appeals.escalate');

        // Teacher Discussions
        Route::get('/discussions', [TeacherDiscussionController::class, 'index'])->name('teacher.discussions.index');
        Route::get('/discussions/create-forum', [TeacherDiscussionController::class, 'createForum'])->name('teacher.discussions.create-forum');
        Route::post('/discussions/forum', [TeacherDiscussionController::class, 'storeForum'])->name('teacher.discussions.store-forum');
        Route::get('/discussions/forum/{forum}', [TeacherDiscussionController::class, 'showForum'])->name('teacher.discussions.forum');
        Route::put('/discussions/forum/{forum}', [TeacherDiscussionController::class, 'updateForum'])->name('teacher.discussions.update-forum');
        Route::delete('/discussions/forum/{forum}', [TeacherDiscussionController::class, 'destroyForum'])->name('teacher.discussions.destroy-forum');
        Route::post('/discussions/forum/{forum}/toggle-lock', [TeacherDiscussionController::class, 'toggleForumLock'])->name('teacher.discussions.toggle-forum-lock');
        Route::get('/discussions/topic/{topic}', [TeacherDiscussionController::class, 'showTopic'])->name('teacher.discussions.topic');
        Route::get('/discussions/forum/{forum}/create-topic', [TeacherDiscussionController::class, 'createTopic'])->name('teacher.discussions.create-topic');
        Route::post('/discussions/forum/{forum}/topic', [TeacherDiscussionController::class, 'storeTopic'])->name('teacher.discussions.store-topic');
        Route::post('/discussions/topic/{topic}/toggle-lock', [TeacherDiscussionController::class, 'toggleTopicLock'])->name('teacher.discussions.toggle-topic-lock');
        Route::post('/discussions/topic/{topic}/toggle-pin', [TeacherDiscussionController::class, 'toggleTopicPin'])->name('teacher.discussions.toggle-topic-pin');
        Route::delete('/discussions/topic/{topic}', [TeacherDiscussionController::class, 'destroyTopic'])->name('teacher.discussions.destroy-topic');
        Route::post('/discussions/topic/{topic}/reply', [TeacherDiscussionController::class, 'storeReply'])->name('teacher.discussions.store-reply');
        Route::post('/discussions/reply/{reply}/mark-best', [TeacherDiscussionController::class, 'markBestAnswer'])->name('teacher.discussions.mark-best');
        Route::post('/discussions/reply/{reply}/unmark-best', [TeacherDiscussionController::class, 'unmarkBestAnswer'])->name('teacher.discussions.unmark-best');
        Route::delete('/discussions/reply/{reply}', [TeacherDiscussionController::class, 'destroyReply'])->name('teacher.discussions.destroy-reply');

        // Teacher Excused Absences
        Route::get('/excused-absences', [TeacherExcusedAbsenceController::class, 'index'])->name('teacher.excused-absences.index');
        Route::get('/excused-absences/{excusedAbsence}', [TeacherExcusedAbsenceController::class, 'show'])->name('teacher.excused-absences.show');
        Route::post('/excused-absences/{excusedAbsence}/approve', [TeacherExcusedAbsenceController::class, 'approve'])->name('teacher.excused-absences.approve');
        Route::post('/excused-absences/{excusedAbsence}/reject', [TeacherExcusedAbsenceController::class, 'reject'])->name('teacher.excused-absences.reject');

        // Teacher Accommodations
        Route::get('/accommodations', [TeacherAccommodationController::class, 'index'])->name('teacher.accommodations.index');
        Route::get('/accommodations/student/{student}', [TeacherAccommodationController::class, 'showStudent'])->name('teacher.accommodations.student');
        Route::get('/accommodations/quiz/{assessment}', [TeacherAccommodationController::class, 'showQuiz'])->name('teacher.accommodations.quiz');
        Route::post('/accommodations/quiz/{assessment}/apply', [TeacherAccommodationController::class, 'applyToQuiz'])->name('teacher.accommodations.apply-quiz');
        Route::delete('/accommodations/quiz-accommodation/{quizAccommodation}', [TeacherAccommodationController::class, 'removeFromQuiz'])->name('teacher.accommodations.remove-quiz');
        Route::post('/accommodations/quiz/{assessment}/bulk-apply', [TeacherAccommodationController::class, 'bulkApplyToQuiz'])->name('teacher.accommodations.bulk-apply-quiz');

        // Teacher Reports
        Route::get('/reports', [TeacherReportController::class, 'index'])->name('teacher.reports.index');
        Route::get('/reports/student-progress', [TeacherReportController::class, 'studentProgress'])->name('teacher.reports.student-progress');
        Route::get('/reports/class-performance/{offeringId}/export', [TeacherReportController::class, 'exportClassPerformance'])->name('teacher.reports.export-class-performance');
        Route::get('/reports/student-progress/{offeringId}/{studentId}/export', [TeacherReportController::class, 'exportStudentProgress'])->name('teacher.reports.export-student-progress');
    });

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/grades', [StudentGradeController::class, 'index'])->name('student.grades');

        // Student Courses
        Route::get('/courses', [StudentCourseController::class, 'myCourses'])->name('student.courses.index');
        Route::get('/courses/browse', [StudentCourseController::class, 'browse'])->name('student.courses.browse');
        Route::post('/courses/enroll', [StudentCourseController::class, 'enroll'])->name('student.courses.enroll');
        Route::delete('/courses/drop/{enrollment}', [StudentCourseController::class, 'drop'])->name('student.courses.drop');
        Route::get('/courses/{offering}', [StudentCourseController::class, 'show'])->name('student.courses.show');
        Route::get('/courses/{offering}/materials', [StudentCourseController::class, 'materials'])->name('student.courses.materials');
        Route::get('/courses/{offering}/grades', [StudentCourseController::class, 'grades'])->name('student.courses.grades');
        Route::get('/courses/{offering}/attendance', [StudentCourseController::class, 'attendance'])->name('student.courses.attendance');
        Route::get('/courses/{offering}/participants', [StudentCourseController::class, 'participants'])->name('student.courses.participants');

        // Student Fees
        Route::get('/fees', [StudentPaymentController::class, 'fees'])->name('student.fees.index');

        // Student Payments
        Route::get('/payments', [StudentPaymentController::class, 'index'])->name('student.payments.index');
        Route::post('/payments', [StudentPaymentController::class, 'store'])->name('student.payments.store');

        // Stripe Payment Routes
        Route::get('/payments/stripe/{payment}', [StudentPaymentController::class, 'stripeCheckout'])->name('student.payments.stripe.checkout');
        Route::post('/payments/stripe/webhook', [StudentPaymentController::class, 'stripeWebhook'])->name('student.payments.stripe.webhook');

        // PayPal Payment Routes
        Route::get('/payments/paypal/{payment}', [StudentPaymentController::class, 'paypalCheckout'])->name('student.payments.paypal.checkout');
        Route::get('/payments/paypal/success/{payment}', [StudentPaymentController::class, 'paypalSuccess'])->name('student.payments.paypal.success');
        Route::get('/payments/paypal/cancel/{payment}', [StudentPaymentController::class, 'paypalCancel'])->name('student.payments.paypal.cancel');
        Route::get('/payments/success', [StudentPaymentController::class, 'index'])->name('student.payments.success');
        Route::get('/payments/cancel', [StudentPaymentController::class, 'index'])->name('student.payments.cancel');

        // Student Medical
        Route::get('/medical', [StudentMedicalController::class, 'profile'])->name('student.medical.profile');
        Route::get('/medical/edit', [StudentMedicalController::class, 'edit'])->name('student.medical.edit');
        Route::put('/medical', [StudentMedicalController::class, 'update'])->name('student.medical.update');
        Route::post('/medical/documents', [StudentMedicalController::class, 'uploadDocument'])->name('student.medical.upload-document');
        Route::delete('/medical/documents/{documentId}', [StudentMedicalController::class, 'deleteDocument'])->name('student.medical.delete-document');
        Route::get('/medical/documents/{documentId}/download', [StudentMedicalController::class, 'downloadDocument'])->name('student.medical.download-document');

        // Student Transcript
        Route::get('/transcript', [StudentTranscriptController::class, 'index'])->name('student.transcript.index');
        Route::get('/transcript/export-pdf', [StudentTranscriptController::class, 'exportPdf'])->name('student.transcript.export-pdf');

        // Student Quizzes
        Route::get('/quizzes', [QuizController::class, 'index'])->name('student.quizzes.index');
        Route::get('/quizzes/{assessment}', [QuizController::class, 'show'])->name('student.quizzes.show');
        Route::post('/quizzes/{assessment}/start', [QuizController::class, 'start'])->name('student.quizzes.start');
        Route::get('/quizzes/{assessment}/take', [QuizController::class, 'take'])->name('student.quizzes.take');
        Route::post('/quizzes/{assessment}/save-answer', [QuizController::class, 'saveAnswer'])->name('student.quizzes.save-answer');
        Route::post('/quizzes/{assessment}/submit', [QuizController::class, 'submit'])->name('student.quizzes.submit');
        Route::get('/quizzes/{assessment}/result/{attempt}', [QuizController::class, 'result'])->name('student.quizzes.result');
        Route::get('/quizzes/{assessment}/attempts', [QuizController::class, 'attempts'])->name('student.quizzes.attempts');

        // Student Grade Appeals
        Route::get('/appeals', [StudentGradeAppealController::class, 'index'])->name('student.appeals.index');
        Route::get('/appeals/create', [StudentGradeAppealController::class, 'create'])->name('student.appeals.create');
        Route::post('/appeals', [StudentGradeAppealController::class, 'store'])->name('student.appeals.store');
        Route::get('/appeals/{appeal}', [StudentGradeAppealController::class, 'show'])->name('student.appeals.show');
        Route::get('/appeals/{appeal}/edit', [StudentGradeAppealController::class, 'edit'])->name('student.appeals.edit');
        Route::put('/appeals/{appeal}', [StudentGradeAppealController::class, 'update'])->name('student.appeals.update');
        Route::delete('/appeals/{appeal}', [StudentGradeAppealController::class, 'withdraw'])->name('student.appeals.withdraw');

        // Student Discussions
        Route::get('/discussions', [StudentDiscussionController::class, 'index'])->name('student.discussions.index');
        Route::get('/discussions/course/{offeringId}', [StudentDiscussionController::class, 'courseForums'])->name('student.discussions.course');
        Route::get('/discussions/forum/{forum}', [StudentDiscussionController::class, 'showForum'])->name('student.discussions.forum');
        Route::get('/discussions/topic/{topic}', [StudentDiscussionController::class, 'showTopic'])->name('student.discussions.topic');
        Route::get('/discussions/forum/{forum}/create-topic', [StudentDiscussionController::class, 'createTopic'])->name('student.discussions.create-topic');
        Route::post('/discussions/forum/{forum}/topic', [StudentDiscussionController::class, 'storeTopic'])->name('student.discussions.store-topic');
        Route::post('/discussions/topic/{topic}/reply', [StudentDiscussionController::class, 'storeReply'])->name('student.discussions.store-reply');
        Route::get('/discussions/reply/{reply}/edit', [StudentDiscussionController::class, 'editReply'])->name('student.discussions.edit-reply');
        Route::put('/discussions/reply/{reply}', [StudentDiscussionController::class, 'updateReply'])->name('student.discussions.update-reply');
        Route::delete('/discussions/reply/{reply}', [StudentDiscussionController::class, 'destroyReply'])->name('student.discussions.destroy-reply');

        // Student Medical Leaves
        Route::get('/medical-leaves', [StudentMedicalLeaveController::class, 'index'])->name('student.medical-leaves.index');
        Route::get('/medical-leaves/create', [StudentMedicalLeaveController::class, 'create'])->name('student.medical-leaves.create');
        Route::post('/medical-leaves', [StudentMedicalLeaveController::class, 'store'])->name('student.medical-leaves.store');
        Route::get('/medical-leaves/{medicalLeave}', [StudentMedicalLeaveController::class, 'show'])->name('student.medical-leaves.show');
        Route::get('/medical-leaves/{medicalLeave}/edit', [StudentMedicalLeaveController::class, 'edit'])->name('student.medical-leaves.edit');
        Route::put('/medical-leaves/{medicalLeave}', [StudentMedicalLeaveController::class, 'update'])->name('student.medical-leaves.update');
        Route::delete('/medical-leaves/{medicalLeave}', [StudentMedicalLeaveController::class, 'destroy'])->name('student.medical-leaves.destroy');

        // Student Excused Absences
        Route::get('/excused-absences', [StudentExcusedAbsenceController::class, 'index'])->name('student.excused-absences.index');
        Route::get('/excused-absences/create', [StudentExcusedAbsenceController::class, 'create'])->name('student.excused-absences.create');
        Route::post('/excused-absences', [StudentExcusedAbsenceController::class, 'store'])->name('student.excused-absences.store');
        Route::get('/excused-absences/{excusedAbsence}', [StudentExcusedAbsenceController::class, 'show'])->name('student.excused-absences.show');
        Route::delete('/excused-absences/{excusedAbsence}', [StudentExcusedAbsenceController::class, 'destroy'])->name('student.excused-absences.destroy');

        // Student Course Feedback
        Route::get('/feedback', [StudentCourseFeedbackController::class, 'index'])->name('student.feedback.index');
        Route::get('/feedback/create', [StudentCourseFeedbackController::class, 'create'])->name('student.feedback.create');
        Route::post('/feedback', [StudentCourseFeedbackController::class, 'store'])->name('student.feedback.store');
        Route::get('/feedback/{feedback}', [StudentCourseFeedbackController::class, 'show'])->name('student.feedback.show');
        Route::get('/feedback/{feedback}/edit', [StudentCourseFeedbackController::class, 'edit'])->name('student.feedback.edit');
        Route::put('/feedback/{feedback}', [StudentCourseFeedbackController::class, 'update'])->name('student.feedback.update');
    });

    // Default dashboard - redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    // User Profile Routes (available to all authenticated users)
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    });

    // Global Search API
    Route::middleware(['auth'])->group(function () {
        Route::get('/api/search', [SearchController::class, 'globalSearch'])->name('api.search');
    });

    // User Productivity Features (Available to all authenticated users)
    Route::middleware(['auth'])->group(function () {
        // Tasks
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggle');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Reminders
        Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
        Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
        Route::patch('/reminders/{reminder}/mark-as-read', [ReminderController::class, 'markAsRead'])->name('reminders.markAsRead');
        Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

        // Calendar
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
        Route::put('/calendar/{event}', [CalendarController::class, 'update'])->name('calendar.update');
        Route::delete('/calendar/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');
        Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');

        // Notes
        Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
        Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::patch('/notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.togglePin');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

        // Messages
        // Enhanced Messaging System
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

        // Conversations
        Route::get('/messages/conversation/{conversation}', [MessageController::class, 'showConversation'])->name('messages.conversation');
        Route::post('/messages/conversation', [MessageController::class, 'createConversation'])->name('messages.conversation.create');
        Route::patch('/messages/conversation/{conversation}/read', [MessageController::class, 'markConversationAsRead'])->name('messages.conversation.read');
        Route::patch('/messages/conversation/{conversation}/archive', [MessageController::class, 'archiveConversation'])->name('messages.conversation.archive');
        Route::patch('/messages/conversation/{conversation}/unarchive', [MessageController::class, 'unarchiveConversation'])->name('messages.conversation.unarchive');
        Route::post('/messages/conversation/{conversation}/send', [MessageController::class, 'sendMessage'])->name('messages.send');
        Route::get('/messages/conversation/{conversation}/search', [MessageController::class, 'searchConversation'])->name('messages.conversation.search');

        // Messages
        Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
        Route::patch('/messages/{message}/star', [MessageController::class, 'toggleStar'])->name('messages.toggleStar');
        Route::post('/messages/{message}/reaction', [MessageController::class, 'addReaction'])->name('messages.addReaction');
        Route::delete('/messages/{message}/reaction', [MessageController::class, 'removeReaction'])->name('messages.removeReaction');
        Route::patch('/messages/{message}/pin', [MessageController::class, 'pinMessage'])->name('messages.pin');
        Route::patch('/messages/{message}/unpin', [MessageController::class, 'unpinMessage'])->name('messages.unpin');
        Route::post('/messages/conversation/{conversation}/typing/start', [MessageController::class, 'startTyping'])->name('messages.typing.start');
        Route::post('/messages/conversation/{conversation}/typing/stop', [MessageController::class, 'stopTyping'])->name('messages.typing.stop');
        Route::patch('/messages/read-all', [MessageController::class, 'markAllAsRead'])->name('messages.markAllAsRead');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Search and Templates
        Route::get('/messages/search', [MessageController::class, 'search'])->name('messages.search');
        Route::get('/messages/templates', [MessageController::class, 'getTemplates'])->name('messages.templates');

        // API endpoints for real-time messaging
        Route::post('/api/messages/send', [MessageController::class, 'apiSendMessage'])->name('api.messages.send');

        // Notifications API
        Route::prefix('api/notifications')->middleware('throttle:60,1')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('api.notifications.index');
            Route::get('/unread', [NotificationController::class, 'unread'])->name('api.notifications.unread');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('api.notifications.destroy');
            Route::post('/sound/toggle', [NotificationController::class, 'toggleSound'])->name('api.notifications.sound.toggle');
        });
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';

// Main settings route
Route::middleware(['auth'])->get('/settings', function () {
    return redirect()->route('profile.edit');
})->name('settings');
