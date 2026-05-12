<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'type',
        'category',
        'subject',
        'content',
        'email_content',
        'variables',
        'is_active',
        'send_email',
        'send_push',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'send_email' => 'boolean',
        'send_push' => 'boolean',
    ];

    /**
     * Render template with variables.
     */
    public function render(array $variables = []): string
    {
        $content = $this->content;

        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }

    /**
     * Render email content with variables.
     */
    public function renderEmail(array $variables = []): string
    {
        $content = $this->email_content ?? $this->content;

        foreach ($variables as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }

    /**
     * Get template by key.
     */
    public static function getByKey(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    /**
     * Get templates by category.
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)->where('is_active', true)->get();
    }
}
