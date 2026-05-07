/**
 * UX Enhancements for LMS
 *
 * This file contains JavaScript for:
 * - Keyboard navigation detection
 * - Button loading states
 * - Form submission handling
 * - Dark mode persistence
 * - Notification sound handling
 * - Focus management
 * - Skeleton loaders
 * - File upload progress
 * - Accessibility helpers
 * - Form validation
 * - Live region updates
 * - Smooth transitions
 * - Touch interactions
 */

// ==================== KEYBOARD NAVIGATION DETECTION ====================

(function () {
    "use strict";

    // Detect if user is using keyboard or mouse
    let usingKeyboard = false;

    // Add class to body when using keyboard
    document.addEventListener("keydown", function (e) {
        if (e.key === "Tab") {
            usingKeyboard = true;
            document.body.classList.remove("using-mouse");
            document.body.classList.add("using-keyboard");
        }
    });

    // Remove class when using mouse
    document.addEventListener("mousedown", function () {
        usingKeyboard = false;
        document.body.classList.remove("using-keyboard");
        document.body.classList.add("using-mouse");
    });

    // Initial state
    document.body.classList.add("using-mouse");

    // Expose state
    window.isUsingKeyboard = function () {
        return usingKeyboard;
    };
})();

// ==================== BUTTON LOADING STATES ====================

(function () {
    "use strict";

    // Add loading state to buttons on form submission
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (form.dataset.noLoadingState) return;

        const submitButtons = form.querySelectorAll(
            'button[type="submit"], input[type="submit"]',
        );

        submitButtons.forEach(function (button) {
            // Store original state
            button.setAttribute("data-original-text", button.innerHTML);
            button.setAttribute("data-original-disabled", button.disabled);

            // Disable button
            button.disabled = true;

            // Add loading class
            button.classList.add("loading");

            // Create loading indicator
            const loadingContent = createLoadingContent(button);
            button.innerHTML = loadingContent;
        });
    });

    // Reset button state on page show (for back button)
    window.addEventListener("pageshow", function (e) {
        document
            .querySelectorAll("button.loading, .btn.loading")
            .forEach(function (button) {
                resetButtonState(button);
            });
    });

    /**
     * Create loading content for button
     */
    function createLoadingContent(button) {
        const loadingText =
            button.getAttribute("data-loading-text") || "Loading...";
        const spinnerSize = button.classList.contains("btn-sm")
            ? "w-3 h-3"
            : button.classList.contains("btn-lg")
              ? "w-5 h-5"
              : "w-4 h-4";

        return `
            <span class="loading-spinner ${spinnerSize} border-2 border-current border-t-transparent rounded-full animate-spin inline-block" aria-hidden="true"></span>
            <span class="ml-2">${loadingText}</span>
        `;
    }

    /**
     * Reset button to original state
     */
    function resetButtonState(button) {
        button.disabled =
            button.getAttribute("data-original-disabled") === "true";
        button.classList.remove("loading");

        const originalText = button.getAttribute("data-original-text");
        if (originalText) {
            button.innerHTML = originalText;
        }

        button.removeAttribute("data-original-text");
        button.removeAttribute("data-original-disabled");
    }

    // Expose functions
    window.setButtonLoading = function (button, isLoading) {
        if (isLoading) {
            button.setAttribute("data-original-text", button.innerHTML);
            button.disabled = true;
            button.classList.add("loading");
            button.innerHTML = createLoadingContent(button);
        } else {
            resetButtonState(button);
        }
    };
})();

// ==================== DARK MODE HANDLING ====================

(function () {
    "use strict";

    const DARK_MODE_KEY = "dark_mode";
    const SYSTEM_THEME_KEY = "system_theme_detection";

    /**
     * Initialize dark mode
     */
    function initDarkMode() {
        // Check localStorage
        const savedMode = localStorage.getItem(DARK_MODE_KEY);

        if (savedMode !== null) {
            // Apply saved preference
            document.documentElement.classList.toggle(
                "dark",
                savedMode === "true",
            );
        } else {
            // Check system preference
            const prefersDark = window.matchMedia(
                "(prefers-color-scheme: dark)",
            ).matches;
            document.documentElement.classList.toggle("dark", prefersDark);
        }

        // Listen for system theme changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia(
                "(prefers-color-scheme: dark)",
            );

            mediaQuery.addEventListener("change", function (e) {
                // Only apply if user hasn't set a preference
                if (localStorage.getItem(DARK_MODE_KEY) === null) {
                    const systemThemeDetection =
                        localStorage.getItem(SYSTEM_THEME_KEY) !== "false";

                    if (systemThemeDetection) {
                        document.documentElement.classList.toggle(
                            "dark",
                            e.matches,
                        );
                    }
                }
            });
        }
    }

    /**
     * Toggle dark mode
     */
    function toggleDarkMode() {
        const isDark = document.documentElement.classList.toggle("dark");
        localStorage.setItem(DARK_MODE_KEY, isDark);

        // Dispatch event
        window.dispatchEvent(
            new CustomEvent("darkmode-change", {
                detail: { isDark: isDark },
            }),
        );

        return isDark;
    }

    /**
     * Set dark mode
     */
    function setDarkMode(isDark) {
        document.documentElement.classList.toggle("dark", isDark);
        localStorage.setItem(DARK_MODE_KEY, isDark);

        window.dispatchEvent(
            new CustomEvent("darkmode-change", {
                detail: { isDark: isDark },
            }),
        );
    }

    /**
     * Get current dark mode state
     */
    function isDarkMode() {
        return document.documentElement.classList.contains("dark");
    }

    // Initialize
    initDarkMode();

    // Listen for Livewire events
    if (typeof Livewire !== "undefined") {
        Livewire.on("dark-mode-toggled", function (data) {
            setDarkMode(data.dark_mode);
        });
    }

    // Expose functions
    window.toggleDarkMode = toggleDarkMode;
    window.setDarkMode = setDarkMode;
    window.isDarkMode = isDarkMode;
})();

// ==================== NOTIFICATION SOUND ====================

(function () {
    "use strict";

    const NOTIFICATION_SOUND_KEY = "notification_sound";
    let audioContext = null;

    /**
     * Initialize audio context
     */
    function initAudioContext() {
        if (!audioContext) {
            try {
                audioContext = new (
                    window.AudioContext || window.webkitAudioContext
                )();
            } catch (e) {
                console.warn("Web Audio API not supported:", e);
            }
        }
        return audioContext;
    }

    /**
     * Play notification sound
     */
    function playNotificationSound(options = {}) {
        const {
            frequency = 800,
            duration = 0.3,
            type = "sine",
            volume = 0.3,
        } = options;

        const soundEnabled =
            localStorage.getItem(NOTIFICATION_SOUND_KEY) !== "false";

        if (!soundEnabled) return;

        try {
            const ctx = initAudioContext();
            if (!ctx) return;

            // Resume audio context if suspended
            if (ctx.state === "suspended") {
                ctx.resume();
            }

            const oscillator = ctx.createOscillator();
            const gainNode = ctx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            oscillator.frequency.value = frequency;
            oscillator.type = type;

            gainNode.gain.setValueAtTime(volume, ctx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(
                0.01,
                ctx.currentTime + duration,
            );

            oscillator.start(ctx.currentTime);
            oscillator.stop(ctx.currentTime + duration);
        } catch (e) {
            console.warn("Could not play notification sound:", e);
        }
    }

    /**
     * Play success sound
     */
    function playSuccessSound() {
        playNotificationSound({ frequency: 880, duration: 0.2 });
        setTimeout(
            () => playNotificationSound({ frequency: 1100, duration: 0.2 }),
            150,
        );
    }

    /**
     * Play error sound
     */
    function playErrorSound() {
        playNotificationSound({
            frequency: 300,
            duration: 0.4,
            type: "square",
        });
    }

    /**
     * Toggle notification sound
     */
    function toggleNotificationSound() {
        const current =
            localStorage.getItem(NOTIFICATION_SOUND_KEY) !== "false";
        localStorage.setItem(NOTIFICATION_SOUND_KEY, !current);
        return !current;
    }

    /**
     * Check if notification sound is enabled
     */
    function isNotificationSoundEnabled() {
        return localStorage.getItem(NOTIFICATION_SOUND_KEY) !== "false";
    }

    // Listen for Livewire events
    if (typeof Livewire !== "undefined") {
        Livewire.on("notification-received", function () {
            playNotificationSound();
        });
    }

    // Expose functions
    window.playNotificationSound = playNotificationSound;
    window.playSuccessSound = playSuccessSound;
    window.playErrorSound = playErrorSound;
    window.toggleNotificationSound = toggleNotificationSound;
    window.isNotificationSoundEnabled = isNotificationSoundEnabled;
})();

// ==================== FOCUS MANAGEMENT ====================

(function () {
    "use strict";

    /**
     * Trap focus within element
     */
    function trapFocus(element) {
        const focusableSelectors = [
            "button:not([disabled])",
            "[href]",
            "input:not([disabled])",
            "select:not([disabled])",
            "textarea:not([disabled])",
            '[tabindex]:not([tabindex="-1"])',
        ].join(", ");

        const focusableElements = element.querySelectorAll(focusableSelectors);

        if (focusableElements.length === 0) return;

        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        function handleKeydown(e) {
            if (e.key !== "Tab") return;

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    lastFocusable.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    firstFocusable.focus();
                    e.preventDefault();
                }
            }
        }

        element.addEventListener("keydown", handleKeydown);

        // Return cleanup function
        return function untrapFocus() {
            element.removeEventListener("keydown", handleKeydown);
        };
    }

    /**
     * Auto-focus on modals when they open
     */
    function initModalFocus() {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === "attributes" &&
                    mutation.attributeName === "open"
                ) {
                    const modal = mutation.target;
                    if (modal.hasAttribute("open")) {
                        const focusable = modal.querySelector(
                            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
                        );
                        if (focusable) {
                            setTimeout(() => focusable.focus(), 50);
                        }
                        trapFocus(modal);
                    }
                }
            });
        });

        // Observe all modals
        document
            .querySelectorAll('dialog, [role="dialog"]')
            .forEach(function (modal) {
                observer.observe(modal, { attributes: true });
            });
    }

    /**
     * Focus first error in form
     */
    function focusFirstError(form) {
        const errorElement = form.querySelector(
            '.error, [aria-invalid="true"], .border-red-500',
        );
        if (errorElement) {
            errorElement.focus();
        }
    }

    // Initialize modal focus
    initModalFocus();

    // Expose functions
    window.trapFocus = trapFocus;
    window.focusFirstError = focusFirstError;
})();

// ==================== SKELETON LOADER ====================

(function () {
    "use strict";

    /**
     * Replace skeleton loaders with content
     */
    function replaceSkeleton(containerId, content) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = content;
            container.classList.remove("skeleton");
            container.removeAttribute("data-skeleton-type");
        }
    }

    /**
     * Show skeleton loader
     */
    function showSkeleton(containerId, skeletonType = "default") {
        const container = document.getElementById(containerId);
        if (container) {
            container.setAttribute("data-skeleton-type", skeletonType);
            container.classList.add("skeleton");

            // Add skeleton content based on type
            const skeletonContent = getSkeletonContent(skeletonType);
            if (skeletonContent) {
                container.innerHTML = skeletonContent;
            }
        }
    }

    /**
     * Get skeleton content based on type
     */
    function getSkeletonContent(type) {
        const skeletons = {
            default:
                '<div class="skeleton h-4 w-full mb-2"></div><div class="skeleton h-4 w-3/4"></div>',
            card: `
                <div class="skeleton h-32 w-full mb-4"></div>
                <div class="skeleton h-4 w-3/4 mb-2"></div>
                <div class="skeleton h-4 w-1/2 mb-2"></div>
                <div class="skeleton h-8 w-1/3"></div>
            `,
            list: `
                <div class="flex items-center gap-3 mb-3">
                    <div class="skeleton h-10 w-10 rounded-full"></div>
                    <div class="flex-1">
                        <div class="skeleton h-4 w-1/2 mb-2"></div>
                        <div class="skeleton h-3 w-3/4"></div>
                    </div>
                </div>
            `,
            table: `
                <div class="skeleton h-10 w-full mb-2"></div>
                <div class="skeleton h-10 w-full mb-2"></div>
                <div class="skeleton h-10 w-full mb-2"></div>
            `,
            text: '<div class="skeleton h-4 w-full mb-2"></div>'.repeat(3),
            avatar: '<div class="skeleton h-12 w-12 rounded-full"></div>',
            image: '<div class="skeleton h-48 w-full"></div>',
            button: '<div class="skeleton h-10 w-24 rounded"></div>',
        };

        return skeletons[type] || skeletons.default;
    }

    /**
     * Create skeleton element
     */
    function createSkeleton(options = {}) {
        const {
            width = "100%",
            height = "1rem",
            borderRadius = "4px",
            className = "",
        } = options;

        const skeleton = document.createElement("div");
        skeleton.className = `skeleton ${className}`;
        skeleton.style.width = width;
        skeleton.style.height = height;
        skeleton.style.borderRadius = borderRadius;

        return skeleton;
    }

    // Expose functions
    window.replaceSkeleton = replaceSkeleton;
    window.showSkeleton = showSkeleton;
    window.createSkeleton = createSkeleton;
})();

// ==================== FILE UPLOAD PROGRESS ====================

(function () {
    "use strict";

    /**
     * Track file upload progress
     */
    function trackFileUpload(fileInput, progressBarId, options = {}) {
        const progressBar = document.getElementById(progressBarId);
        const {
            onProgress = null,
            onComplete = null,
            onError = null,
        } = options;

        if (!fileInput.files.length > 0 || !progressBar) return null;

        const file = fileInput.files[0];
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener("progress", function (e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;

                // Update progress bar
                progressBar.style.width = percentComplete + "%";
                progressBar.setAttribute("aria-valuenow", percentComplete);

                // Update text
                const progressText =
                    progressBar.querySelector(".progress-text");
                if (progressText) {
                    progressText.textContent = `${Math.round(percentComplete)}%`;
                }

                // Callback
                if (onProgress) {
                    onProgress(percentComplete, e.loaded, e.total);
                }

                if (percentComplete === 100) {
                    progressBar.classList.add("complete");
                    if (onComplete) {
                        onComplete();
                    }
                }
            }
        });

        xhr.addEventListener("error", function () {
            progressBar.classList.add("error");
            if (onError) {
                onError();
            }
        });

        return xhr;
    }

    /**
     * Initialize file upload with drag and drop
     */
    function initFileUpload(dropZoneId, fileInputId, options = {}) {
        const dropZone = document.getElementById(dropZoneId);
        const fileInput = document.getElementById(fileInputId);

        if (!dropZone || !fileInput) return;

        // Drag events
        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop zone when dragging over
        ["dragenter", "dragover"].forEach((eventName) => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add("drag-over");
            });
        });

        ["dragleave", "drop"].forEach((eventName) => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove("drag-over");
            });
        });

        // Handle dropped files
        dropZone.addEventListener("drop", (e) => {
            const files = e.dataTransfer.files;
            fileInput.files = files;

            // Trigger change event
            fileInput.dispatchEvent(new Event("change", { bubbles: true }));

            if (options.onDrop) {
                options.onDrop(files);
            }
        });

        // Click to upload
        dropZone.addEventListener("click", () => {
            fileInput.click();
        });

        // Handle file selection
        fileInput.addEventListener("change", () => {
            if (options.onSelect) {
                options.onSelect(fileInput.files);
            }
        });
    }

    // Expose functions
    window.trackFileUpload = trackFileUpload;
    window.initFileUpload = initFileUpload;
})();

// ==================== ACCESSIBILITY HELPERS ====================

(function () {
    "use strict";

    /**
     * Announce message to screen readers
     */
    function announceToScreenReader(message, priority = "polite") {
        const announcement = document.createElement("div");
        announcement.setAttribute("role", "status");
        announcement.setAttribute("aria-live", priority);
        announcement.setAttribute("aria-atomic", "true");
        announcement.className = "sr-only";
        announcement.textContent = message;

        document.body.appendChild(announcement);

        setTimeout(function () {
            document.body.removeChild(announcement);
        }, 1000);
    }

    /**
     * Handle escape key for closing modals/dropdowns
     */
    function initEscapeHandler() {
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                // Close any open dropdowns
                document
                    .querySelectorAll(
                        "[data-dropdown].open, [data-flux-dropdown].open",
                    )
                    .forEach(function (dropdown) {
                        dropdown.classList.remove("open");
                    });

                // Close any open modals (if not handled by the modal itself)
                const openModal = document.querySelector(
                    'dialog[open], [role="dialog"][aria-hidden="false"]',
                );
                if (openModal && typeof openModal.close === "function") {
                    // Let the modal handle it
                }
            }
        });
    }

    /**
     * Get focusable elements within container
     */
    function getFocusableElements(container) {
        const selectors = [
            "button:not([disabled])",
            "[href]",
            "input:not([disabled])",
            "select:not([disabled])",
            "textarea:not([disabled])",
            '[tabindex]:not([tabindex="-1"])',
        ].join(", ");

        return Array.from(container.querySelectorAll(selectors));
    }

    /**
     * Move focus to next focusable element
     */
    function moveFocusToNext(currentElement) {
        const focusable = getFocusableElements(document.body);
        const currentIndex = focusable.indexOf(currentElement);

        if (currentIndex > -1 && currentIndex < focusable.length - 1) {
            focusable[currentIndex + 1].focus();
        }
    }

    /**
     * Move focus to previous focusable element
     */
    function moveFocusToPrevious(currentElement) {
        const focusable = getFocusableElements(document.body);
        const currentIndex = focusable.indexOf(currentElement);

        if (currentIndex > 0) {
            focusable[currentIndex - 1].focus();
        }
    }

    // Initialize
    initEscapeHandler();

    // Expose functions
    window.announceToScreenReader = announceToScreenReader;
    window.getFocusableElements = getFocusableElements;
    window.moveFocusToNext = moveFocusToNext;
    window.moveFocusToPrevious = moveFocusToPrevious;
})();

// ==================== FORM VALIDATION ====================

(function () {
    "use strict";

    /**
     * Initialize form validation
     */
    function initFormValidation() {
        document.addEventListener("DOMContentLoaded", function () {
            document
                .querySelectorAll("form[data-validate]")
                .forEach(function (form) {
                    // Add novalidate to let JS handle validation
                    form.setAttribute("novalidate", "");

                    // Submit handler
                    form.addEventListener("submit", function (e) {
                        if (!validateForm(form)) {
                            e.preventDefault();
                        }
                    });

                    // Real-time validation
                    form.querySelectorAll("input, select, textarea").forEach(
                        function (field) {
                            field.addEventListener("blur", function () {
                                validateField(this);
                            });

                            field.addEventListener("input", function () {
                                if (
                                    this.classList.contains("error") ||
                                    this.getAttribute("aria-invalid") === "true"
                                ) {
                                    validateField(this);
                                }
                            });
                        },
                    );
                });
        });
    }

    /**
     * Validate form
     */
    function validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll(
            '[required], [aria-required="true"]',
        );

        requiredFields.forEach(function (field) {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            // Focus first invalid field
            const firstInvalid = form.querySelector('[aria-invalid="true"]');
            if (firstInvalid) {
                firstInvalid.focus();
            }

            // Announce error to screen readers
            announceToScreenReader(
                "Form has validation errors. Please correct them and try again.",
            );
        }

        return isValid;
    }

    /**
     * Validate field
     */
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = "";

        // Required check
        if (field.required && !value) {
            isValid = false;
            errorMessage =
                field.getAttribute("data-required-message") ||
                "This field is required";
        }

        // Email validation
        if (isValid && field.type === "email" && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = "Please enter a valid email address";
            }
        }

        // Pattern validation
        if (isValid && field.pattern && value) {
            const regex = new RegExp(field.pattern);
            if (!regex.test(value)) {
                isValid = false;
                errorMessage =
                    field.getAttribute("data-pattern-message") ||
                    "Invalid format";
            }
        }

        // Min length
        if (isValid && field.minLength > 0 && value.length < field.minLength) {
            isValid = false;
            errorMessage = `Minimum ${field.minLength} characters required`;
        }

        // Max length
        if (isValid && field.maxLength > 0 && value.length > field.maxLength) {
            isValid = false;
            errorMessage = `Maximum ${field.maxLength} characters allowed`;
        }

        // Custom validation
        if (isValid && field.dataset.validate) {
            const customResult = customValidation(field);
            if (customResult !== true) {
                isValid = false;
                errorMessage = customResult;
            }
        }

        // Update UI
        updateFieldValidationUI(field, isValid, errorMessage);

        return isValid;
    }

    /**
     * Custom validation
     */
    function customValidation(field) {
        const validateType = field.dataset.validate;

        switch (validateType) {
            case "phone":
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                return (
                    phoneRegex.test(field.value) ||
                    "Please enter a valid phone number"
                );
            case "url":
                try {
                    new URL(field.value);
                    return true;
                } catch {
                    return "Please enter a valid URL";
                }
            case "date":
                return (
                    !isNaN(Date.parse(field.value)) ||
                    "Please enter a valid date"
                );
            default:
                return true;
        }
    }

    /**
     * Update field validation UI
     */
    function updateFieldValidationUI(field, isValid, errorMessage) {
        // Remove existing error message
        const existingError = field.parentNode.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }

        if (isValid) {
            field.classList.remove("error", "border-red-500");
            field.removeAttribute("aria-invalid");
            field.removeAttribute("aria-describedby");
        } else {
            field.classList.add("error", "border-red-500");
            field.setAttribute("aria-invalid", "true");

            // Add error message
            const errorEl = document.createElement("p");
            errorEl.className = "error-message text-red-500 text-sm mt-1";
            errorEl.setAttribute("role", "alert");
            errorEl.id = `error-${field.name || Date.now()}`;
            errorEl.textContent = errorMessage;

            field.parentNode.appendChild(errorEl);
            field.setAttribute("aria-describedby", errorEl.id);
        }
    }

    // Initialize
    initFormValidation();

    // Expose functions
    window.validateForm = validateForm;
    window.validateField = validateField;
})();

// ==================== LIVE REGION UPDATES ====================

(function () {
    "use strict";

    // Create live region for dynamic content updates
    const liveRegion = document.createElement("div");
    liveRegion.id = "live-region";
    liveRegion.setAttribute("role", "status");
    liveRegion.setAttribute("aria-live", "polite");
    liveRegion.setAttribute("aria-atomic", "true");
    liveRegion.className = "sr-only";
    document.body.appendChild(liveRegion);

    // Create assertive live region for urgent announcements
    const assertiveRegion = document.createElement("div");
    assertiveRegion.id = "live-region-assertive";
    assertiveRegion.setAttribute("role", "alert");
    assertiveRegion.setAttribute("aria-live", "assertive");
    assertiveRegion.setAttribute("aria-atomic", "true");
    assertiveRegion.className = "sr-only";
    document.body.appendChild(assertiveRegion);

    /**
     * Update live region
     */
    function updateLiveRegion(message, assertive = false) {
        const region = assertive ? assertiveRegion : liveRegion;
        region.textContent = "";

        // Small delay to ensure screen readers pick up the change
        setTimeout(() => {
            region.textContent = message;
        }, 50);
    }

    /**
     * Clear live region
     */
    function clearLiveRegion() {
        liveRegion.textContent = "";
        assertiveRegion.textContent = "";
    }

    // Expose functions
    window.updateLiveRegion = updateLiveRegion;
    window.clearLiveRegion = clearLiveRegion;
})();

// ==================== SMOOTH TRANSITIONS ====================

(function () {
    "use strict";

    /**
     * Fade in element
     */
    function fadeIn(element, duration = 300) {
        element.style.opacity = "0";
        element.style.display = "";

        let start = null;

        function animate(timestamp) {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const opacity = Math.min(progress / duration, 1);

            element.style.opacity = opacity;

            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        }

        requestAnimationFrame(animate);
    }

    /**
     * Fade out element
     */
    function fadeOut(element, duration = 300) {
        let start = null;

        function animate(timestamp) {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const opacity = 1 - Math.min(progress / duration, 1);

            element.style.opacity = opacity;

            if (progress < duration) {
                requestAnimationFrame(animate);
            } else {
                element.style.display = "none";
            }
        }

        requestAnimationFrame(animate);
    }

    /**
     * Slide element
     */
    function slide(element, direction = "up", duration = 300) {
        const directions = {
            up: { from: "translateY(20px)", to: "translateY(0)" },
            down: { from: "translateY(-20px)", to: "translateY(0)" },
            left: { from: "translateX(20px)", to: "translateX(0)" },
            right: { from: "translateX(-20px)", to: "translateX(0)" },
        };

        const { from, to } = directions[direction] || directions.up;

        element.style.transform = from;
        element.style.opacity = "0";
        element.style.display = "";

        let start = null;

        function animate(timestamp) {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const percent = Math.min(progress / duration, 1);

            element.style.transform = `translateY(${20 * (1 - percent)}px)`;
            element.style.opacity = percent;

            if (progress < duration) {
                requestAnimationFrame(animate);
            } else {
                element.style.transform = to;
                element.style.opacity = "1";
            }
        }

        requestAnimationFrame(animate);
    }

    // Expose functions
    window.fadeIn = fadeIn;
    window.fadeOut = fadeOut;
    window.slide = slide;
})();

// ==================== TOUCH INTERACTIONS ====================

(function () {
    "use strict";

    /**
     * Initialize touch interactions
     */
    function initTouchInteractions() {
        // Add touch feedback to buttons
        document.querySelectorAll(".btn, button").forEach(function (btn) {
            btn.addEventListener("touchstart", function () {
                this.classList.add("touch-active");
            });

            btn.addEventListener("touchend", function () {
                this.classList.remove("touch-active");
            });

            btn.addEventListener("touchcancel", function () {
                this.classList.remove("touch-active");
            });
        });

        // Swipe detection
        initSwipeDetection();
    }

    /**
     * Initialize swipe detection
     */
    function initSwipeDetection() {
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;

        document.addEventListener("touchstart", function (e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        });

        document.addEventListener("touchend", function (e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;

            handleSwipe(touchStartX, touchStartY, touchEndX, touchEndY);
        });

        function handleSwipe(startX, startY, endX, endY) {
            const threshold = 50;
            const diffX = endX - startX;
            const diffY = endY - startY;

            if (Math.abs(diffX) > Math.abs(diffY)) {
                // Horizontal swipe
                if (Math.abs(diffX) > threshold) {
                    const direction = diffX > 0 ? "right" : "left";

                    // Dispatch custom event
                    document.dispatchEvent(
                        new CustomEvent("swipe", {
                            detail: { direction: direction },
                        }),
                    );

                    // Handle sidebar swipe
                    if (direction === "right" && startX < 30) {
                        const sidebar = document.getElementById("sidebar");
                        const overlay =
                            document.querySelector(".sidebar-overlay");
                        if (sidebar && window.innerWidth < 1024) {
                            sidebar.classList.add("open");
                            if (overlay) overlay.classList.add("active");
                        }
                    }

                    if (direction === "left") {
                        const sidebar = document.getElementById("sidebar");
                        const overlay =
                            document.querySelector(".sidebar-overlay");
                        if (sidebar && sidebar.classList.contains("open")) {
                            sidebar.classList.remove("open");
                            if (overlay) overlay.classList.remove("active");
                        }
                    }
                }
            } else {
                // Vertical swipe
                if (Math.abs(diffY) > threshold) {
                    const direction = diffY > 0 ? "down" : "up";

                    document.dispatchEvent(
                        new CustomEvent("swipe", {
                            detail: { direction: direction },
                        }),
                    );
                }
            }
        }
    }

    // Initialize
    if ("ontouchstart" in window) {
        initTouchInteractions();
    }
})();

// ==================== INTERSECTION OBSERVER UTILITIES ====================

(function () {
    "use strict";

    /**
     * Create intersection observer
     */
    function createIntersectionObserver(callback, options = {}) {
        const defaultOptions = {
            root: null,
            rootMargin: "0px",
            threshold: 0.1,
        };

        return new IntersectionObserver(callback, {
            ...defaultOptions,
            ...options,
        });
    }

    /**
     * Observe element visibility
     */
    function observeVisibility(element, callback, options = {}) {
        const observer = createIntersectionObserver((entries) => {
            entries.forEach((entry) => {
                callback(entry.isIntersecting, entry);
            });
        }, options);

        observer.observe(element);

        return observer;
    }

    /**
     * Lazy load element
     */
    function lazyLoad(element, loadCallback) {
        const observer = observeVisibility(
            element,
            (isVisible, entry) => {
                if (isVisible) {
                    loadCallback(element);
                    observer.unobserve(element);
                }
            },
            { rootMargin: "100px" },
        );

        return observer;
    }

    // Expose functions
    window.createIntersectionObserver = createIntersectionObserver;
    window.observeVisibility = observeVisibility;
    window.lazyLoad = lazyLoad;
})();

// ==================== EXPORT ====================

// Export all functions for use in other scripts
window.UXEnhancements = {
    // Dark mode
    toggleDarkMode: window.toggleDarkMode,
    setDarkMode: window.setDarkMode,
    isDarkMode: window.isDarkMode,

    // Notifications
    playNotificationSound: window.playNotificationSound,
    playSuccessSound: window.playSuccessSound,
    playErrorSound: window.playErrorSound,
    toggleNotificationSound: window.toggleNotificationSound,
    isNotificationSoundEnabled: window.isNotificationSoundEnabled,

    // Focus management
    trapFocus: window.trapFocus,
    focusFirstError: window.focusFirstError,
    getFocusableElements: window.getFocusableElements,
    moveFocusToNext: window.moveFocusToNext,
    moveFocusToPrevious: window.moveFocusToPrevious,

    // Skeleton loaders
    replaceSkeleton: window.replaceSkeleton,
    showSkeleton: window.showSkeleton,
    createSkeleton: window.createSkeleton,

    // File upload
    trackFileUpload: window.trackFileUpload,
    initFileUpload: window.initFileUpload,

    // Accessibility
    announceToScreenReader: window.announceToScreenReader,
    updateLiveRegion: window.updateLiveRegion,
    clearLiveRegion: window.clearLiveRegion,

    // Form validation
    validateForm: window.validateForm,
    validateField: window.validateField,

    // Transitions
    fadeIn: window.fadeIn,
    fadeOut: window.fadeOut,
    slide: window.slide,

    // Intersection observer
    createIntersectionObserver: window.createIntersectionObserver,
    observeVisibility: window.observeVisibility,
    lazyLoad: window.lazyLoad,

    // Button loading
    setButtonLoading: window.setButtonLoading,
};
