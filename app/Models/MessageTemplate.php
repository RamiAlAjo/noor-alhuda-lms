<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'category',
        'is_active',
        'is_public',
        'created_by',
        'usage_count',
        'variables',
        'tags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'usage_count' => 'integer',
        'variables' => 'array',
        'tags' => 'array',
    ];

    /**
     * Template categories
     */
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_ACADEMIC = 'academic';
    const CATEGORY_ADMINISTRATIVE = 'administrative';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_WELCOME = 'welcome';

    /**
     * Get the creator of the template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
     * Render subject with variables.
     */
    public function renderSubject(array $variables = []): string
    {
        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{{$key}}}", $value, $subject);
        }

        return $subject;
    }

    /**
     * Extract variables from template content.
     */
    public function extractVariables(): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $this->content . ' ' . $this->subject, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get available variables as formatted list.
     */
    public function getFormattedVariablesAttribute(): array
    {
        $variables = $this->variables ?? $this->extractVariables();

        return array_map(function ($variable) {
            return [
                'name' => $variable,
                'description' => $this->getVariableDescription($variable),
            ];
        }, $variables);
    }

    /**
     * Get description for a variable.
     */
    private function getVariableDescription(string $variable): string
    {
        $descriptions = [
            'user_name' => 'Recipient\'s full name',
            'user_email' => 'Recipient\'s email address',
            'sender_name' => 'Sender\'s full name',
            'sender_email' => 'Sender\'s email address',
            'course_name' => 'Course name',
            'course_code' => 'Course code',
            'assignment_name' => 'Assignment name',
            'due_date' => 'Due date',
            'grade' => 'Grade value',
            'semester_name' => 'Semester name',
            'department_name' => 'Department name',
            'current_date' => 'Current date',
            'current_time' => 'Current time',
        ];

        return $descriptions[$variable] ?? 'Custom variable';
    }

    /**
     * Create a message from this template.
     */
    public function createMessage(array $variables = []): Message
    {
        $this->incrementUsage();

        return Message::create([
            'sender_id' => auth()->id(),
            'subject' => $this->renderSubject($variables),
            'content' => $this->render($variables),
            'message_type' => Message::TYPE_TEMPLATE,
            'metadata' => [
                'template_id' => $this->id,
                'template_name' => $this->name,
                'variables_used' => $variables,
            ],
        ]);
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public templates.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for templates by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for templates created by user.
     */
    public function scopeCreatedBy($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Get most used templates.
     */
    public static function getMostUsed(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get templates for user (public + own).
     */
    public static function getAvailableForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
                    ->where(function ($query) use ($userId) {
                        $query->where('is_public', true)
                              ->orWhere('created_by', $userId);
                    })
                    ->orderBy('usage_count', 'desc')
                    ->get();
    }
}
