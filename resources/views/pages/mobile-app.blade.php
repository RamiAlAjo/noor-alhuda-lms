<x-layouts::app :title="__('Mobile App')">
    <div class="mx-auto max-w-4xl">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Mobile App') }}</h1>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Download the Noor Alhuda LMS mobile app for the best learning experience on your phone') }}</p>
        </div>

        @if(config('settings.mobile_app_enabled') && config('settings.mobile_app_download_url'))
            <!-- QR Code Section -->
            <div class="mb-8 rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <h2 class="mb-6 text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Scan QR Code') }}</h2>

                <div class="flex flex-col items-center justify-center gap-8 md:flex-row">
                    <!-- QR Code -->
                    <div class="rounded-xl bg-white p-4 shadow-inner">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(config('settings.mobile_app_download_url')) }}"
                             alt="QR Code"
                             class="h-48 w-48"
                             loading="lazy">
                    </div>

                    <!-- Download Links -->
                    <div class="space-y-4">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Or download directly:') }}</p>

                        <div class="flex flex-col gap-3">
                            @if(config('settings.mobile_app_android_url'))
                                <a href="{{ config('settings.mobile_app_android_url') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.523 2.047a.5.5 0 00-.583.13L4.186 14.94a.5.5 0 00.342.86h4.098l1.425-4.337a.5.5 0 01.608-.255l2.37 1.938 1.59-4.853a.5.5 0 01.788.088l-1.063 3.236 1.427 1.427a.5.5 0 00.707-.707l-1.887-1.887.817-2.485a.5.5 0 01.825 0l2.393 3.767a.5.5 0 00.707.103l1.962-.982a.5.5 0 00.13-.546z"/>
                                        <path d="M4.5 11.5A.5.5 0 015 11h3a.5.5 0 010 1H5a.5.5 0 01-.5-.5z"/>
                                    </svg>
                                    {{ __('Download for Android') }}
                                </a>
                            @endif

                            @if(config('settings.mobile_app_ios_url'))
                                <a href="{{ config('settings.mobile_app_ios_url') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-neutral-800 px-6 py-3 font-semibold text-white transition hover:bg-neutral-900 dark:bg-neutral-700 dark:hover:bg-neutral-600">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                    {{ __('Download for iOS') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Access Anywhere') }}</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Access your courses, assignments, and grades from anywhere') }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="mb-2 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Push Notifications') }}</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Get instant notifications about deadlines and announcements') }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Offline Access') }}</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Download materials for offline viewing') }}</p>
                </div>
            </div>
        @else
            <!-- Mobile App Not Available -->
            <div class="rounded-xl border border-neutral-200 bg-white p-8 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </div>
                <h2 class="mb-2 text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Mobile App Coming Soon') }}</h2>
                <p class="text-neutral-500 dark:text-neutral-400">{{ __('The mobile app is currently under development. Stay tuned for updates!') }}</p>
            </div>
        @endif
    </div>
</x-layouts::app>
