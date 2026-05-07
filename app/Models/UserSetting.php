<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'base_theme',
        'appearance',
        'locale',
        'high_contrast',
        'large_text',
        'dyslexia_font',
        'reduced_motion',
        'grayscale',
        'strong_focus_outline',
        'line_spacing',
        // Additional Moodle-like settings
        'background_color',
        'text_color',
        'font_face',
        'font_size',
        'font_kerning',
        'letter_spacing',
        'image_visibility',
        'link_highlight',
        'text_alignment',
        // Gradient settings
        'light_gradient',
        'dark_gradient',
    ];

    protected $casts = [
        'high_contrast' => 'boolean',
        'large_text' => 'boolean',
        'dyslexia_font' => 'boolean',
        'reduced_motion' => 'boolean',
        'grayscale' => 'boolean',
        'strong_focus_outline' => 'boolean',
        'line_spacing' => 'float',
        'image_visibility' => 'boolean',
        'notification_sound' => 'boolean',
        'dark_mode' => 'boolean',
        'system_theme_detection' => 'boolean',
    ];

    protected $attributes = [
        'theme' => 'zinc',
        'base_theme' => 'default-dark',
        'appearance' => 'dark',
        'locale' => 'en',
        'high_contrast' => false,
        'large_text' => false,
        'dyslexia_font' => false,
        'reduced_motion' => false,
        'grayscale' => false,
        'strong_focus_outline' => false,
        'line_spacing' => 1.5,
        'background_color' => '#ffffff',
        'text_color' => '#1f2937',
        'font_face' => 'sans-serif',
        'font_size' => 16,
        'font_kerning' => 'normal',
        'letter_spacing' => 'normal',
        'image_visibility' => true,
        'link_highlight' => 'none',
        'text_alignment' => 'left',
        'light_gradient' => 'indigo',
        'dark_gradient' => 'indigo',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if RTL is enabled based on locale.
     */
    public function isRtl(): bool
    {
        return in_array($this->locale, ['ar']);
    }

    /**
     * Get all accessibility settings as array.
     */
    public function getAccessibilitySettings(): array
    {
        return [
            'high_contrast' => $this->high_contrast,
            'large_text' => $this->large_text,
            'dyslexia_font' => $this->dyslexia_font,
            'reduced_motion' => $this->reduced_motion,
            'grayscale' => $this->grayscale,
            'strong_focus_outline' => $this->strong_focus_outline,
            'line_spacing' => $this->line_spacing,
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'font_face' => $this->font_face,
            'font_size' => $this->font_size,
            'font_kerning' => $this->font_kerning,
            'letter_spacing' => $this->letter_spacing,
            'image_visibility' => $this->image_visibility,
            'link_highlight' => $this->link_highlight,
            'text_alignment' => $this->text_alignment,
            'is_rtl' => $this->isRtl(),
        ];
    }

    /**
     * Get font face options.
     */
    public static function getFontFaceOptions(): array
    {
        return [
            'sans-serif' => __('Sans Serif'),
            'serif' => __('Serif'),
            'dyslexic' => __('Dyslexic'),
            'monospace' => __('Monospace'),
        ];
    }

    /**
     * Get font size options.
     */
    public static function getFontSizeOptions(): array
    {
        return [
            12 => __('Small (12px)'),
            14 => __('Medium (14px)'),
            16 => __('Normal (16px)'),
            18 => __('Large (18px)'),
            20 => __('Extra Large (20px)'),
            24 => __('Huge (24px)'),
        ];
    }

    /**
     * Get text alignment options.
     */
    public static function getTextAlignmentOptions(): array
    {
        return [
            'left' => __('Left'),
            'center' => __('Center'),
            'right' => __('Right'),
            'justify' => __('Justify'),
        ];
    }

    /**
     * Get link highlight options.
     */
    public static function getLinkHighlightOptions(): array
    {
        return [
            'none' => __('None'),
            'underline' => __('Underline'),
            'background' => __('Background'),
            'bold' => __('Bold'),
        ];
    }
}
