<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Message',
                'subject' => 'Welcome to Noor Alhuda LMS, {{user_name}}!',
                'content' => 'Dear {{user_name}},

Welcome to Noor Alhuda LMS! We\'re excited to have you join our learning community.

Your account has been successfully created and you can now access all course materials, assignments, and announcements.

Here are some quick tips to get started:
• Complete your profile information
• Browse available courses
• Join your class groups
• Check upcoming assignments

If you have any questions, feel free to contact our support team.

Best regards,
Noor Alhuda LMS Team',
                'category' => MessageTemplate::CATEGORY_WELCOME,
                'is_public' => true,
                'created_by' => 1, // Assuming admin user exists
                'variables' => ['user_name'],
                'tags' => ['welcome', 'onboarding', 'getting-started'],
            ],
            [
                'name' => 'Assignment Reminder',
                'subject' => 'Assignment Due Soon: {{assignment_name}}',
                'content' => 'Hi {{user_name}},

This is a reminder that your assignment "{{assignment_name}}" for {{course_name}} is due in {{days_left}} days.

Due Date: {{due_date}}

Please make sure to submit your assignment before the deadline to avoid any penalties.

Best regards,
{{sender_name}}',
                'category' => MessageTemplate::CATEGORY_ACADEMIC,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'assignment_name', 'course_name', 'days_left', 'due_date', 'sender_name'],
                'tags' => ['assignment', 'reminder', 'deadline', 'academic'],
            ],
            [
                'name' => 'Grade Posted',
                'subject' => 'Grade Posted for {{assignment_name}}',
                'content' => 'Hello {{user_name}},

Your grade has been posted for "{{assignment_name}}" in {{course_name}}.

Grade: {{grade}}

You can view your detailed grade report in the course materials section.

Keep up the good work!

Best regards,
{{sender_name}}',
                'category' => MessageTemplate::CATEGORY_ACADEMIC,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'assignment_name', 'course_name', 'grade', 'sender_name'],
                'tags' => ['grade', 'feedback', 'academic', 'assessment'],
            ],
            [
                'name' => 'Course Announcement',
                'subject' => 'New Announcement: {{course_name}}',
                'content' => 'Dear {{user_name}},

There\'s a new announcement in {{course_name}}:

{{announcement_content}}

Please check the course page for more details and any attached materials.

Best regards,
{{sender_name}}',
                'category' => MessageTemplate::CATEGORY_ACADEMIC,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'course_name', 'announcement_content', 'sender_name'],
                'tags' => ['announcement', 'course', 'update', 'academic'],
            ],
            [
                'name' => 'Payment Reminder',
                'subject' => 'Payment Reminder - Noor Alhuda LMS',
                'content' => 'Dear {{user_name}},

This is a reminder about your outstanding payment for Noor Alhuda LMS.

Amount Due: {{amount}}
Due Date: {{due_date}}

Please complete your payment to avoid any service interruptions.

You can make payments through:
• Online payment portal
• Bank transfer
• Office payment

Contact our finance department if you have any questions.

Best regards,
Finance Department
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_ADMINISTRATIVE,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'amount', 'due_date'],
                'tags' => ['payment', 'reminder', 'finance', 'administrative'],
            ],
            [
                'name' => 'System Maintenance',
                'subject' => 'Scheduled System Maintenance',
                'content' => 'Dear {{user_name}},

We will be performing scheduled maintenance on Noor Alhuda LMS.

Maintenance Window: {{start_time}} to {{end_time}}
Expected Duration: {{duration}}

During this time, the system may be temporarily unavailable. We apologize for any inconvenience.

What to expect:
{{maintenance_details}}

We recommend saving your work before the maintenance begins.

Thank you for your patience.

Best regards,
IT Department
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_SYSTEM,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'start_time', 'end_time', 'duration', 'maintenance_details'],
                'tags' => ['maintenance', 'system', 'downtime', 'technical'],
            ],
            [
                'name' => 'Enrollment Confirmation',
                'subject' => 'Enrollment Confirmed - {{course_name}}',
                'content' => 'Congratulations {{user_name}}!

Your enrollment in {{course_name}} has been confirmed.

Course Details:
• Course Code: {{course_code}}
• Semester: {{semester_name}}
• Start Date: {{start_date}}

You can now:
• Access course materials
• View assignments and deadlines
• Participate in discussions
• Contact your instructor

Welcome to the course!

Best regards,
Academic Department
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_ACADEMIC,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'course_name', 'course_code', 'semester_name', 'start_date'],
                'tags' => ['enrollment', 'confirmation', 'course', 'academic'],
            ],
            [
                'name' => 'Password Reset',
                'subject' => 'Password Reset Request',
                'content' => 'Hi {{user_name}},

We received a request to reset your password for your Noor Alhuda LMS account.

If you made this request, click the link below to reset your password:
{{reset_link}}

This link will expire in 24 hours for security reasons.

If you didn\'t request a password reset, please ignore this email. Your password will remain unchanged.

For security reasons, never share this email or the reset link with anyone.

Best regards,
Security Team
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_SYSTEM,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'reset_link'],
                'tags' => ['password', 'reset', 'security', 'account'],
            ],
            [
                'name' => 'Account Activation',
                'subject' => 'Activate Your Noor Alhuda LMS Account',
                'content' => 'Welcome {{user_name}}!

Your Noor Alhuda LMS account has been created and needs to be activated.

Please click the link below to activate your account:
{{activation_link}}

Once activated, you can:
• Access your courses
• View your schedule
• Communicate with instructors
• Track your progress

If you have any questions, please contact our support team.

Best regards,
Support Team
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_WELCOME,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'activation_link'],
                'tags' => ['activation', 'account', 'welcome', 'onboarding'],
            ],
            [
                'name' => 'Certificate Ready',
                'subject' => 'Your Certificate is Ready - {{course_name}}',
                'content' => 'Congratulations {{user_name}}!

Your certificate for completing {{course_name}} is now ready for download.

Certificate Details:
• Course: {{course_name}}
• Completion Date: {{completion_date}}
• Grade: {{grade}}

You can download your certificate from:
{{certificate_link}}

This accomplishment demonstrates your dedication to learning and professional development.

Keep up the excellent work!

Best regards,
Academic Department
Noor Alhuda LMS',
                'category' => MessageTemplate::CATEGORY_ACADEMIC,
                'is_public' => true,
                'created_by' => 1,
                'variables' => ['user_name', 'course_name', 'completion_date', 'grade', 'certificate_link'],
                'tags' => ['certificate', 'completion', 'achievement', 'academic'],
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
