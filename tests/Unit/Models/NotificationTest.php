<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_notification_with_valid_data()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'type' => 'grade',
            'title' => 'Grade Posted',
            'content' => 'Your grade for Math 101 has been posted.',
            'link' => '/grades/123',
            'is_read' => false,
            'data' => ['course_id' => 123, 'grade' => 'A'],
        ];

        $notification = Notification::create($data);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'grade',
            'title' => 'Grade Posted',
            'content' => 'Your grade for Math 101 has been posted.',
            'link' => '/grades/123',
            'is_read' => false,
        ]);
        $this->assertInstanceOf(Notification::class, $notification);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    /** @test */
    public function it_can_be_marked_as_read()
    {
        $notification = Notification::factory()->create(['is_read' => false]);

        $notification->markAsRead();

        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_be_marked_as_unread()
    {
        $notification = Notification::factory()->create(['is_read' => true, 'read_at' => now()]);

        $notification->markAsUnread();

        $this->assertFalse($notification->fresh()->is_read);
        $this->assertNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_filter_unread_notifications()
    {
        Notification::factory()->count(3)->create(['is_read' => false]);
        Notification::factory()->count(2)->create(['is_read' => true]);

        $unreadNotifications = Notification::unread()->get();

        $this->assertCount(3, $unreadNotifications);
        $unreadNotifications->each(function ($notification) {
            $this->assertFalse($notification->is_read);
        });
    }

    /** @test */
    public function it_can_filter_read_notifications()
    {
        Notification::factory()->count(2)->create(['is_read' => true]);
        Notification::factory()->count(3)->create(['is_read' => false]);

        $readNotifications = Notification::read()->get();

        $this->assertCount(2, $readNotifications);
        $readNotifications->each(function ($notification) {
            $this->assertTrue($notification->is_read);
        });
    }

    /** @test */
    public function it_can_filter_by_type()
    {
        Notification::factory()->count(2)->create(['type' => 'grade']);
        Notification::factory()->count(3)->create(['type' => 'enrollment']);
        Notification::factory()->count(1)->create(['type' => 'payment']);

        $gradeNotifications = Notification::ofType('grade')->get();

        $this->assertCount(2, $gradeNotifications);
        $gradeNotifications->each(function ($notification) {
            $this->assertEquals('grade', $notification->type);
        });
    }

    /** @test */
    public function it_can_get_recent_notifications()
    {
        Notification::factory()->count(15)->create();

        $recentNotifications = Notification::recent(5)->get();

        $this->assertCount(5, $recentNotifications);
    }

    /** @test */
    public function it_can_create_notification_for_user_statically()
    {
        $user = User::factory()->create();

        $notification = Notification::createForUser(
            $user,
            'grade',
            'Grade Posted',
            'Your grade has been posted',
            '/grades/123',
            ['course_id' => 123]
        );

        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals('grade', $notification->type);
        $this->assertEquals('Grade Posted', $notification->title);
        $this->assertEquals('Your grade has been posted', $notification->content);
        $this->assertEquals('/grades/123', $notification->link);
        $this->assertEquals(['course_id' => 123, 'icon' => 'academic-cap', 'color' => 'indigo'], $notification->data);
    }

    /** @test */
    public function it_returns_correct_type_config()
    {
        $gradeConfig = Notification::getTypeConfig('grade');
        $this->assertEquals('academic-cap', $gradeConfig['icon']);
        $this->assertEquals('indigo', $gradeConfig['color']);
        $this->assertEquals('Grade', $gradeConfig['label']);

        $unknownConfig = Notification::getTypeConfig('unknown');
        $this->assertEquals('bell', $unknownConfig['icon']);
        $this->assertEquals('slate', $unknownConfig['color']);
        $this->assertEquals('Notification', $unknownConfig['label']);
    }

    /** @test */
    public function it_returns_correct_icon_attribute()
    {
        $notification = Notification::factory()->create(['type' => 'grade']);
        $this->assertEquals('academic-cap', $notification->icon);

        $notificationWithCustomIcon = Notification::factory()->create([
            'type' => 'grade',
            'data' => ['icon' => 'custom-icon'],
        ]);
        $this->assertEquals('custom-icon', $notificationWithCustomIcon->icon);
    }

    /** @test */
    public function it_returns_correct_color_attribute()
    {
        $notification = Notification::factory()->create(['type' => 'enrollment']);
        $this->assertEquals('green', $notification->color);

        $notificationWithCustomColor = Notification::factory()->create([
            'type' => 'enrollment',
            'data' => ['color' => 'custom-color'],
        ]);
        $this->assertEquals('custom-color', $notificationWithCustomColor->color);
    }

    /** @test */
    public function it_returns_correct_type_label_attribute()
    {
        $notification = Notification::factory()->create(['type' => 'payment']);
        $this->assertEquals('Payment', $notification->type_label);

        $notificationUnknown = Notification::factory()->create(['type' => 'unknown']);
        $this->assertEquals('Notification', $notificationUnknown->type_label);
    }

    /** @test */
    public function it_returns_time_ago_attribute()
    {
        $notification = Notification::factory()->create();

        $this->assertIsString($notification->time_ago);
        $this->assertStringContainsString('ago', $notification->time_ago);
    }

    /** @test */
    public function it_checks_if_notification_is_new()
    {
        $newNotification = Notification::factory()->create(['created_at' => now()->subMinutes(30)]);
        $this->assertTrue($newNotification->isNew());

        $oldNotification = Notification::factory()->create(['created_at' => now()->subHours(2)]);
        $this->assertFalse($oldNotification->isNew());
    }
}
