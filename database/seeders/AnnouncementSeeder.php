<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to Noor Alhuda LMS',
                'title_ar' => 'مرحباً بك في نور الهدى',
                'content' => 'We are excited to welcome you to our Learning Management System. Explore your courses, stay updated with announcements, and make the most of your educational journey.',
                'content_ar' => 'نحن متحمسون لرحبت بك في نظام إدارة التعلم الخاص بنا. استكشف دوركك، وابق على اطلاع بالإعلانات، واستفد максимально من رحلتك التعليمية.',
            ],
            [
                'title' => 'Fall Semester Registration Open',
                'title_ar' => 'فتح التسجيل للفصل الدراسي الخريفي',
                'content' => 'Registration for the Fall semester is now open. Please log in to register for your courses before the deadline.',
                'content_ar' => 'التسجيل للفصل الدراسي الخريفي مفتوح الآن. يرجى تسجيل الدخول للتسجيل في دورسك قبل الموعد النهائي.',
            ],
            [
                'title' => 'System Maintenance Notice',
                'title_ar' => 'إشعار صيانة النظام',
                'content' => 'The system will undergo maintenance on Saturday from 2:00 AM to 6:00 AM. Some features may be temporarily unavailable.',
                'content_ar' => 'سيخضع النظام للصيانة يوم السبت من الساعة 2:00 صباحاً إلى الساعة 6:00 صباحاً. قد تكون بعض الميزات غير متاحة مؤقتاً.',
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['title' => $announcement['title']],
                [
                    'user_id' => 1,
                    'title_ar' => $announcement['title_ar'],
                    'content' => $announcement['content'],
                    'content_ar' => $announcement['content_ar'],
                    'is_published' => true,
                ]
            );
        }
    }
}
