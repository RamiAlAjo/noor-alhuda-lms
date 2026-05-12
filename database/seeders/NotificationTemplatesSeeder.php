<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'grade_posted',
                'name' => 'Grade Posted',
                'type' => 'notification',
                'category' => 'grade',
                'subject' => 'New Grade Posted - {{course_name}}',
                'content' => 'Your grade for {{assessment_name}} in {{course_name}} has been posted: {{grade}}',
                'email_content' => '<p>Dear {{student_name}},</p><p>Your grade for <strong>{{assessment_name}}</strong> in <strong>{{course_name}}</strong> has been posted.</p><p><strong>Grade: {{grade}}</strong></p><p>You can view your complete grade report by logging into your account.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'course_name', 'assessment_name', 'grade'],
                'send_email' => true,
                'send_push' => true,
            ],
            [
                'key' => 'enrollment_approved',
                'name' => 'Enrollment Approved',
                'type' => 'notification',
                'category' => 'enrollment',
                'subject' => 'Enrollment Approved - {{course_name}}',
                'content' => 'Your enrollment request for {{course_name}} has been approved.',
                'email_content' => '<p>Dear {{student_name}},</p><p>Great news! Your enrollment request for <strong>{{course_name}}</strong> has been approved.</p><p>You can now access the course materials and start learning.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'course_name'],
                'send_email' => true,
                'send_push' => true,
            ],
            [
                'key' => 'enrollment_rejected',
                'name' => 'Enrollment Rejected',
                'type' => 'notification',
                'category' => 'enrollment',
                'subject' => 'Enrollment Request - {{course_name}}',
                'content' => 'Your enrollment request for {{course_name}} could not be approved at this time.',
                'email_content' => '<p>Dear {{student_name}},</p><p>We regret to inform you that your enrollment request for <strong>{{course_name}}</strong> could not be approved at this time.</p><p>This may be due to course capacity limits or other requirements. Please contact your academic advisor for more information.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'course_name'],
                'send_email' => true,
                'send_push' => true,
            ],
            [
                'key' => 'payment_successful',
                'name' => 'Payment Successful',
                'type' => 'notification',
                'category' => 'payment',
                'subject' => 'Payment Received - {{amount}}',
                'content' => 'Your payment of {{amount}} has been successfully processed.',
                'email_content' => '<p>Dear {{student_name}},</p><p>Your payment of <strong>{{amount}}</strong> has been successfully processed.</p><p>Payment Reference: {{reference}}</p><p>Thank you for your payment. Your account has been updated accordingly.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'amount', 'reference'],
                'send_email' => true,
                'send_push' => true,
            ],
            [
                'key' => 'assignment_due_soon',
                'name' => 'Assignment Due Soon',
                'type' => 'notification',
                'category' => 'reminder',
                'subject' => 'Assignment Due Soon - {{assignment_name}}',
                'content' => 'Your assignment "{{assignment_name}}" in {{course_name}} is due in {{days_left}} days.',
                'email_content' => '<p>Dear {{student_name}},</p><p>This is a reminder that your assignment <strong>"{{assignment_name}}"</strong> in <strong>{{course_name}}</strong> is due in <strong>{{days_left}} days</strong>.</p><p>Due Date: {{due_date}}</p><p>Please make sure to submit your assignment on time to avoid any penalties.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'assignment_name', 'course_name', 'days_left', 'due_date'],
                'send_email' => false,
                'send_push' => true,
            ],
            [
                'key' => 'course_announcement',
                'name' => 'Course Announcement',
                'type' => 'notification',
                'category' => 'announcement',
                'subject' => 'New Announcement - {{course_name}}',
                'content' => 'New announcement in {{course_name}}: {{announcement_title}}',
                'email_content' => '<p>Dear {{student_name}},</p><p>There is a new announcement in <strong>{{course_name}}</strong>:</p><h3>{{announcement_title}}</h3><div>{{announcement_content}}</div><p>Please check the course page for more details.</p><p>Best regards,<br>Noor Alhuda LMS</p>',
                'variables' => ['student_name', 'course_name', 'announcement_title', 'announcement_content'],
                'send_email' => false,
                'send_push' => true,
            ],
            [
                'key' => 'system_maintenance',
                'name' => 'System Maintenance',
                'type' => 'notification',
                'category' => 'system',
                'subject' => 'System Maintenance Notice',
                'content' => 'The system will be under maintenance from {{start_time}} to {{end_time}}.',
                'email_content' => '<p>Dear {{user_name}},</p><p>The Noor Alhuda LMS will be under maintenance from <strong>{{start_time}}</strong> to <strong>{{end_time}}</strong>.</p><p>During this time, the system may be temporarily unavailable. We apologize for any inconvenience.</p><p>Maintenance Details: {{maintenance_details}}</p><p>Best regards,<br>Noor Alhuda LMS Team</p>',
                'variables' => ['user_name', 'start_time', 'end_time', 'maintenance_details'],
                'send_email' => true,
                'send_push' => true,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
