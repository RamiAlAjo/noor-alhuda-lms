<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- Primary Meta Tags -->
<title>{{ $title ?? config('app.name') }}</title>
<meta name="title" content="{{ $title ?? config('app.name') }}">
<meta name="description" content="{{ $description ?? __('lms.learning_management_system') }} - {{ __('lms.meta_description') }}">
<meta name="author" content="{{ config('app.name') }}">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#3b82f6" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#1c1917" media="(prefers-color-scheme: dark)">
<meta name="color-scheme" content="light dark">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $title ?? config('app.name') }}">
<meta property="og:description" content="{{ $description ?? __('lms.learning_management_system') }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="{{ app()->getLocale() ?? 'en_US' }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="{{ $title ?? config('app.name') }}">
<meta property="twitter:description" content="{{ $description ?? __('lms.learning_management_system') }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ url()->current() }}">

<!-- Favicon -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

<!-- DNS Prefetch and Preconnect for Performance -->
<link rel="dns-prefetch" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

<!-- Fonts -->
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- =====================================================
     HEAD PARTIAL - CRITICAL STYLES
     Used by: All pages (included in layouts)
     Description: Dark mode flash prevention, CSS variables for themes,
     reduced motion media queries
===================================================== -->
<!-- Critical CSS for Dark Mode Flash Prevention -->
<style>
    :root { color-scheme: light dark; }
    html.dark { background-color: #0c0a09; color: #fafaf9; }
    html.light { background-color: #ffffff; color: #1c1917; }
    html[data-base-theme="default-dark"],
    html[data-base-theme="cyberpunk-neon"],
    html[data-base-theme="cyberpunk-dark"],
    html[data-base-theme="synthwave"],
    html[data-base-theme="midnight"],
    html[data-base-theme="dracula"],
    html[data-base-theme="nord"] { background-color: var(--theme-background, #0c0a09); color: var(--theme-text, #fafaf9); }
</style>

@vite(['resources/css/app.css', 'resources/css/accessibility.css', 'resources/css/mobile.css', 'resources/js/app.js', 'resources/js/ux-enhancements.js'])
@fluxAppearance

<!-- Accessibility: Reduced Motion -->
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>
