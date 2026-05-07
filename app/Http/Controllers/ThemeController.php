<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ThemeController extends Controller
{
    /**
     * Switch the background gradient.
     */
    public function switchGradient(Request $request)
    {
        $gradient = $request->input('gradient', 'indigo');

        // Validate gradient choice
        $validGradients = [
            'indigo',
            'blue',
            'purple',
            'pink',
            'rose',
            'orange',
            'emerald',
            'teal',
            'cyan',
            'slate',
            // Custom gradients
            'red_black',
            'lightblue_blue',
            'gray_orange',
            'green_black',
            'purple_black',
            'purple_yellow',
            'teal_white',
            'red_white',
        ];

        if (! in_array($gradient, $validGradients)) {
            $gradient = 'indigo';
        }

        // Store in session
        session(['auth_gradient' => $gradient]);

        return back();
    }

    /**
     * Get gradient classes for the view.
     */
    public static function getGradientClasses(): array
    {
        $gradient = session('auth_gradient', 'indigo');

        $gradients = [
            'indigo' => [
                'light' => 'from-indigo-50 via-white to-purple-50',
                'dark' => 'dark:from-indigo-950 dark:via-purple-950/30 dark:to-zinc-900',
            ],
            'blue' => [
                'light' => 'from-blue-50 via-white to-cyan-50',
                'dark' => 'dark:from-blue-950 dark:via-cyan-950/30 dark:to-zinc-900',
            ],
            'purple' => [
                'light' => 'from-purple-50 via-pink-50 to-rose-50',
                'dark' => 'dark:from-purple-950 dark:via-pink-950/30 dark:to-zinc-900',
            ],
            'pink' => [
                'light' => 'from-pink-50 via-rose-50 to-orange-50',
                'dark' => 'dark:from-pink-950 dark:via-rose-950/30 dark:to-zinc-900',
            ],
            'rose' => [
                'light' => 'from-rose-50 via-red-50 to-orange-50',
                'dark' => 'dark:from-rose-950 dark:via-red-950/30 dark:to-zinc-900',
            ],
            'orange' => [
                'light' => 'from-orange-50 via-amber-50 to-yellow-50',
                'dark' => 'dark:from-orange-950 dark:via-amber-950/30 dark:to-zinc-900',
            ],
            'emerald' => [
                'light' => 'from-emerald-50 via-teal-50 to-cyan-50',
                'dark' => 'dark:from-emerald-950 dark:via-teal-950/30 dark:to-zinc-900',
            ],
            'teal' => [
                'light' => 'from-teal-50 via-green-50 to-emerald-50',
                'dark' => 'dark:from-teal-950 dark:via-green-950/30 dark:to-zinc-900',
            ],
            'cyan' => [
                'light' => 'from-cyan-50 via-sky-50 to-blue-50',
                'dark' => 'dark:from-cyan-950 dark:via-sky-950/30 dark:to-zinc-900',
            ],
            'slate' => [
                'light' => 'from-slate-50 via-gray-50 to-zinc-50',
                'dark' => 'dark:from-slate-950 dark:via-gray-950/30 dark:to-zinc-900',
            ],
            // Custom gradients
            'red_black' => [
                'light' => 'from-[#ff2e63] via-[#ff2e63]/50 to-black',
                'dark' => 'dark:from-[#ff2e63] dark:via-black/80 dark:to-black',
            ],
            'lightblue_blue' => [
                'light' => 'from-[#c7d2fe] via-[#3ab0fe]/50 to-[#3ab0fe]',
                'dark' => 'dark:from-[#3ab0fe] dark:via-[#3ab0fe]/80 dark:to-[#3ab0fe]',
            ],
            'gray_orange' => [
                'light' => 'from-[#1f2937] via-[#ffa77f]/50 to-[#ffa77f]',
                'dark' => 'dark:from-[#1f2937] dark:via-[#ffa77f]/80 dark:to-[#ffa77f]',
            ],
            'green_black' => [
                'light' => 'from-[#00ff88] via-[#00ff88]/50 to-black',
                'dark' => 'dark:from-[#00ff88] dark:via-black/80 dark:to-black',
            ],
            'purple_black' => [
                'light' => 'from-[#5c2cf4] via-[#5c2cf4]/50 to-[#040405]',
                'dark' => 'dark:from-[#5c2cf4] dark:via-[#040405]/80 dark:to-[#040405]',
            ],
            'purple_yellow' => [
                'light' => 'from-[#6a00ff] via-[#f7ff00]/50 to-[#f7ff00]',
                'dark' => 'dark:from-[#6a00ff] dark:via-[#f7ff00]/80 dark:to-[#f7ff00]',
            ],
            'teal_white' => [
                'light' => 'from-[#007f5f] via-[#faf9f6]/50 to-[#faf9f6]',
                'dark' => 'dark:from-[#007f5f] dark:via-[#faf9f6]/80 dark:to-[#faf9f6]',
            ],
            'red_white' => [
                'light' => 'from-[#d90429] via-white to-white',
                'dark' => 'dark:from-[#d90429] dark:via-white/80 dark:to-white',
            ],
        ];

        return $gradients[$gradient] ?? $gradients['indigo'];
    }
}
