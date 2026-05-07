/**
 * Noor LMS - JavaScript
 * Main application JavaScript
 *
 * Features:
 * - Accessibility features initialization
 * - Tooltips and modals
 * - Sidebar management
 * - Data tables
 * - Utility functions
 * - Dark mode support
 * - Keyboard navigation
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize all features
    initAccessibility();
    initTooltips();
    initModals();
    initSidebar();
    initDataTables();
    initDarkMode();
    initKeyboardNavigation();
    initLazyLoading();
    initSmoothScroll();
    initFormValidation();
    initCopyToClipboard();
    initDropdowns();
    initTabs();
    initAccordions();
    initSearch();
});

/* ==================== ACCESSIBILITY FEATURES ==================== */

/**
 * Initialize accessibility features
 */
function initAccessibility() {
    // Check for accessibility settings from session
    const settings = {
        highContrast: document.body.dataset.highContrast === "true",
        largeText: document.body.dataset.largeText === "true",
        dyslexiaFont: document.body.dataset.dyslexiaFont === "true",
        reducedMotion: document.body.dataset.reducedMotion === "true",
        grayscale: document.body.dataset.grayscale === "true",
        lineSpacing: document.body.dataset.lineSpacing === "true",
        focusOutline: document.body.dataset.focusOutline === "true",
    };

    // Apply settings to body
    Object.entries(settings).forEach(([key, value]) => {
        if (value) {
            document.body.setAttribute(`data-${key}`, "true");
        }
    });

    // Check system preferences
    checkSystemAccessibilityPreferences();

    // Listen for accessibility toggle changes
    document.addEventListener("click", function (e) {
        const toggle = e.target.closest("[data-accessibility-toggle]");
        if (toggle) {
            handleAccessibilityToggle(toggle);
        }
    });
}

/**
 * Check system accessibility preferences
 */
function checkSystemAccessibilityPreferences() {
    // Check for reduced motion preference
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        if (document.body.dataset.reducedMotion === undefined) {
            document.body.dataset.reducedMotion = "true";
        }
    }

    // Check for high contrast preference
    if (window.matchMedia("(prefers-contrast: high)").matches) {
        if (document.body.dataset.highContrast === undefined) {
            document.body.dataset.highContrast = "true";
        }
    }

    // Check for dark mode preference
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        if (localStorage.getItem("dark_mode") === null) {
            document.documentElement.classList.add("dark");
        }
    }
}

/**
 * Handle accessibility toggle
 */
function handleAccessibilityToggle(toggle) {
    const setting = toggle.dataset.accessibilityToggle;
    const current = document.body.dataset[setting] === "true";
    const newValue = !current;

    document.body.dataset[setting] = newValue;

    // Update toggle state
    toggle.setAttribute("aria-pressed", newValue);

    // Save preference
    saveAccessibilitySetting(setting, newValue);

    // Announce change to screen readers
    announceToScreenReader(
        `${setting.replace(/([A-Z])/g, " $1").toLowerCase()} ${newValue ? "enabled" : "disabled"}`,
    );
}

/**
 * Save accessibility setting to server
 */
function saveAccessibilitySetting(setting, value) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) return;

    fetch("/settings/accessibility/toggle", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.content,
        },
        body: JSON.stringify({ [setting]: value }),
    }).catch((error) => {
        console.error("Failed to save accessibility setting:", error);
    });
}

/* ==================== TOOLTIPS ==================== */

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipTriggers = document.querySelectorAll("[data-tooltip]");

    tooltipTriggers.forEach((trigger) => {
        trigger.addEventListener("mouseenter", showTooltip);
        trigger.addEventListener("mouseleave", hideTooltip);
        trigger.addEventListener("focus", showTooltip);
        trigger.addEventListener("blur", hideTooltip);
    });
}

/**
 * Show tooltip
 */
function showTooltip(e) {
    const trigger = e.target.closest("[data-tooltip]");
    if (!trigger) return;

    const text = trigger.dataset.tooltip;
    const position = trigger.dataset.tooltipPosition || "top";

    const tooltip = document.createElement("div");
    tooltip.className = `tooltip tooltip-${position}`;
    tooltip.textContent = text;
    tooltip.setAttribute("role", "tooltip");
    tooltip.id = `tooltip-${Date.now()}`;

    // Link trigger to tooltip
    trigger.setAttribute("aria-describedby", tooltip.id);

    document.body.appendChild(tooltip);

    // Position tooltip
    positionTooltip(trigger, tooltip, position);

    trigger._tooltip = tooltip;
}

/**
 * Position tooltip relative to trigger
 */
function positionTooltip(trigger, tooltip, position) {
    const rect = trigger.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    const spacing = 8;

    // Reset position
    tooltip.style.top = "";
    tooltip.style.left = "";
    tooltip.style.right = "";
    tooltip.style.bottom = "";

    switch (position) {
        case "top":
            tooltip.style.top = `${rect.top - tooltipRect.height - spacing}px`;
            tooltip.style.left = `${rect.left + rect.width / 2 - tooltipRect.width / 2}px`;
            break;
        case "bottom":
            tooltip.style.top = `${rect.bottom + spacing}px`;
            tooltip.style.left = `${rect.left + rect.width / 2 - tooltipRect.width / 2}px`;
            break;
        case "left":
            tooltip.style.top = `${rect.top + rect.height / 2 - tooltipRect.height / 2}px`;
            tooltip.style.left = `${rect.left - tooltipRect.width - spacing}px`;
            break;
        case "right":
            tooltip.style.top = `${rect.top + rect.height / 2 - tooltipRect.height / 2}px`;
            tooltip.style.left = `${rect.right + spacing}px`;
            break;
    }

    // Ensure tooltip stays within viewport
    const tooltipBounds = tooltip.getBoundingClientRect();
    if (tooltipBounds.left < 0) {
        tooltip.style.left = "0";
    }
    if (tooltipBounds.right > window.innerWidth) {
        tooltip.style.left = `${window.innerWidth - tooltipRect.width - spacing}px`;
    }
    if (tooltipBounds.top < 0) {
        tooltip.style.top = `${rect.bottom + spacing}px`;
    }
}

/**
 * Hide tooltip
 */
function hideTooltip(e) {
    const trigger = e.target.closest("[data-tooltip]");
    if (trigger && trigger._tooltip) {
        trigger._tooltip.remove();
        trigger.removeAttribute("aria-describedby");
        delete trigger._tooltip;
    }
}

/* ==================== MODALS ==================== */

/**
 * Initialize modals
 */
function initModals() {
    const modalTriggers = document.querySelectorAll("[data-modal]");

    modalTriggers.forEach((trigger) => {
        trigger.addEventListener("click", function () {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                openModal(modal);
            }
        });
    });

    // Close modal on backdrop click
    document.querySelectorAll("dialog").forEach((modal) => {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });

    // Close modal on escape key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            const openModal = document.querySelector("dialog[open]");
            if (openModal) {
                closeModal(openModal);
            }
        }
    });

    // Close buttons
    document.querySelectorAll("[data-modal-close]").forEach((btn) => {
        btn.addEventListener("click", function () {
            const modal = this.closest("dialog");
            if (modal) {
                closeModal(modal);
            }
        });
    });
}

/**
 * Open modal
 */
function openModal(modal) {
    // Store last focused element
    modal._lastFocused = document.activeElement;

    // Show modal
    if (typeof modal.showModal === "function") {
        modal.showModal();
    } else {
        modal.setAttribute("open", "");
    }

    // Focus first focusable element
    const focusable = modal.querySelector(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
    );
    if (focusable) {
        setTimeout(() => focusable.focus(), 100);
    }

    // Trap focus
    trapFocus(modal);

    // Prevent body scroll
    document.body.style.overflow = "hidden";
}

/**
 * Close modal
 */
function closeModal(modal) {
    if (typeof modal.close === "function") {
        modal.close();
    } else {
        modal.removeAttribute("open");
    }

    // Restore focus
    if (modal._lastFocused) {
        modal._lastFocused.focus();
    }

    // Restore body scroll
    document.body.style.overflow = "";
}

/**
 * Trap focus within element
 */
function trapFocus(element) {
    const focusableElements = element.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
    );

    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    element.addEventListener("keydown", function (e) {
        if (e.key === "Tab") {
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
    });
}

/* ==================== SIDEBAR ==================== */

/**
 * Initialize sidebar
 */
function initSidebar() {
    const sidebarToggle = document.querySelector("[data-sidebar-toggle]");
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.querySelector(".sidebar-overlay");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            toggleSidebar(sidebar, sidebarOverlay);
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", function () {
            closeSidebar(sidebar, sidebarOverlay);
        });
    }

    // Restore state
    if (localStorage.getItem("sidebarCollapsed") === "true") {
        sidebar.classList.add("collapsed");
        sidebar.classList.remove("expanded");
    }

    // Handle resize
    window.addEventListener(
        "resize",
        debounce(function () {
            if (window.innerWidth >= 1024) {
                closeSidebar(sidebar, sidebarOverlay);
            }
        }, 250),
    );
}

/**
 * Toggle sidebar
 */
function toggleSidebar(sidebar, overlay) {
    const isOpen = sidebar.classList.contains("open");

    if (isOpen) {
        closeSidebar(sidebar, overlay);
    } else {
        openSidebar(sidebar, overlay);
    }
}

/**
 * Open sidebar
 */
function openSidebar(sidebar, overlay) {
    sidebar.classList.add("open");
    sidebar.classList.remove("collapsed");
    if (overlay) overlay.classList.add("active");
    document.body.style.overflow = "hidden";

    // Save state for desktop
    if (window.innerWidth >= 1024) {
        localStorage.setItem("sidebarCollapsed", "false");
    }
}

/**
 * Close sidebar
 */
function closeSidebar(sidebar, overlay) {
    sidebar.classList.remove("open");
    if (overlay) overlay.classList.remove("active");
    document.body.style.overflow = "";

    // Save state for desktop
    if (window.innerWidth >= 1024) {
        sidebar.classList.add("collapsed");
        localStorage.setItem("sidebarCollapsed", "true");
    }
}

/* ==================== DATA TABLES ==================== */

/**
 * Initialize data tables
 */
function initDataTables() {
    const tables = document.querySelectorAll("[data-table]");

    tables.forEach((table) => {
        initTableSearch(table);
        initTableSort(table);
        initTablePagination(table);
    });
}

/**
 * Initialize table search
 */
function initTableSearch(table) {
    const searchInput = document.querySelector(
        `[data-table-search="${table.id}"]`,
    );

    if (searchInput) {
        searchInput.addEventListener(
            "input",
            debounce(function () {
                const term = this.value.toLowerCase();
                const rows = table.querySelectorAll("tbody tr");

                rows.forEach((row) => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? "" : "none";
                });

                // Update row count
                updateTableRowCount(table);
            }, 300),
        );
    }
}

/**
 * Initialize table sorting
 */
function initTableSort(table) {
    const headers = table.querySelectorAll("th[data-sort]");

    headers.forEach((header) => {
        header.addEventListener("click", function () {
            const column = this.dataset.sort;
            const order = this.dataset.order === "asc" ? "desc" : "asc";

            // Update order
            headers.forEach((h) => h.removeAttribute("data-order"));
            this.dataset.order = order;

            // Sort rows
            sortTableRows(table, column, order);
        });

        // Make header keyboard accessible
        header.setAttribute("tabindex", "0");
        header.setAttribute("role", "columnheader");
        header.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                this.click();
            }
        });
    });
}

/**
 * Sort table rows
 */
function sortTableRows(table, column, order) {
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const columnIndex = Array.from(table.querySelectorAll("th")).findIndex(
        (th) => th.dataset.sort === column,
    );

    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex]?.textContent.trim() || "";
        const bValue = b.cells[columnIndex]?.textContent.trim() || "";

        const comparison = aValue.localeCompare(bValue, undefined, {
            numeric: true,
            sensitivity: "base",
        });

        return order === "asc" ? comparison : -comparison;
    });

    rows.forEach((row) => tbody.appendChild(row));
}

/**
 * Initialize table pagination
 */
function initTablePagination(table) {
    const pagination = document.querySelector(
        `[data-table-pagination="${table.id}"]`,
    );

    if (pagination) {
        const pageSize = parseInt(pagination.dataset.pageSize) || 10;
        const rows = table.querySelectorAll("tbody tr");
        const totalPages = Math.ceil(rows.length / pageSize);

        // Store pagination data
        table._pagination = { pageSize, totalPages, currentPage: 1 };

        // Show first page
        showTablePage(table, 1);

        // Create pagination controls
        createPaginationControls(table, pagination);
    }
}

/**
 * Show table page
 */
function showTablePage(table, page) {
    const { pageSize } = table._pagination;
    const rows = table.querySelectorAll("tbody tr");
    const start = (page - 1) * pageSize;
    const end = start + pageSize;

    rows.forEach((row, index) => {
        row.style.display = index >= start && index < end ? "" : "none";
    });

    table._pagination.currentPage = page;
}

/**
 * Create pagination controls
 */
function createPaginationControls(table, container) {
    const { totalPages, currentPage } = table._pagination;

    container.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `pagination-btn ${i === currentPage ? "active" : ""}`;
        btn.addEventListener("click", () => showTablePage(table, i));
        container.appendChild(btn);
    }
}

/**
 * Update table row count
 */
function updateTableRowCount(table) {
    const countEl = document.querySelector(`[data-table-count="${table.id}"]`);

    if (countEl) {
        const visibleRows = table.querySelectorAll(
            'tbody tr:not([style*="display: none"])',
        ).length;
        countEl.textContent = visibleRows;
    }
}

/* ==================== DARK MODE ==================== */

/**
 * Initialize dark mode
 */
function initDarkMode() {
    const darkModeToggle = document.querySelector("[data-dark-mode-toggle]");

    if (darkModeToggle) {
        darkModeToggle.addEventListener("click", toggleDarkMode);
    }

    // Listen for system theme changes
    window
        .matchMedia("(prefers-color-scheme: dark)")
        .addEventListener("change", (e) => {
            if (localStorage.getItem("dark_mode") === null) {
                document.documentElement.classList.toggle("dark", e.matches);
            }
        });
}

/**
 * Toggle dark mode
 */
function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle("dark");
    localStorage.setItem("dark_mode", isDark);

    // Update toggle button
    const toggle = document.querySelector("[data-dark-mode-toggle]");
    if (toggle) {
        toggle.setAttribute("aria-pressed", isDark);
    }

    // Announce change
    announceToScreenReader(`Dark mode ${isDark ? "enabled" : "disabled"}`);
}

/* ==================== KEYBOARD NAVIGATION ==================== */

/**
 * Initialize keyboard navigation
 */
function initKeyboardNavigation() {
    // Detect keyboard vs mouse usage
    document.addEventListener("keydown", function (e) {
        if (e.key === "Tab") {
            document.body.classList.remove("using-mouse");
            document.body.classList.add("using-keyboard");
        }
    });

    document.addEventListener("mousedown", function () {
        document.body.classList.remove("using-keyboard");
        document.body.classList.add("using-mouse");
    });

    // Skip links
    const skipLinks = document.querySelectorAll(".skip-link");
    skipLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.tabIndex = -1;
                target.focus();
            }
        });
    });

    // Escape key handler
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            // Close dropdowns
            document
                .querySelectorAll("[data-dropdown].open")
                .forEach((dropdown) => {
                    dropdown.classList.remove("open");
                });
        }
    });
}

/* ==================== LAZY LOADING ==================== */

/**
 * Initialize lazy loading
 */
function initLazyLoading() {
    const images = document.querySelectorAll("img[data-src]");

    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute("data-src");
                        img.classList.add("loaded");
                        observer.unobserve(img);
                    }
                });
            },
            {
                rootMargin: "50px 0px",
                threshold: 0.01,
            },
        );

        images.forEach((img) => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach((img) => {
            img.src = img.dataset.src;
            img.removeAttribute("data-src");
        });
    }
}

/* ==================== SMOOTH SCROLL ==================== */

/**
 * Initialize smooth scroll
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            const targetId = this.getAttribute("href");
            if (targetId === "#") return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });

                // Update URL
                history.pushState(null, null, targetId);

                // Focus target
                target.tabIndex = -1;
                target.focus();
            }
        });
    });
}

/* ==================== FORM VALIDATION ==================== */

/**
 * Initialize form validation
 */
function initFormValidation() {
    document.querySelectorAll("form[data-validate]").forEach((form) => {
        form.addEventListener("submit", function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        // Real-time validation
        form.querySelectorAll("input, select, textarea").forEach((field) => {
            field.addEventListener("blur", function () {
                validateField(this);
            });

            field.addEventListener("input", function () {
                if (this.classList.contains("error")) {
                    validateField(this);
                }
            });
        });
    });
}

/**
 * Validate form
 */
function validateForm(form) {
    let isValid = true;
    const fields = form.querySelectorAll(
        "input[required], select[required], textarea[required]",
    );

    fields.forEach((field) => {
        if (!validateField(field)) {
            isValid = false;
        }
    });

    if (!isValid) {
        // Focus first invalid field
        const firstInvalid = form.querySelector(".error");
        if (firstInvalid) {
            firstInvalid.focus();
        }

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
    const type = field.type;
    let isValid = true;
    let errorMessage = "";

    // Required check
    if (field.required && !value) {
        isValid = false;
        errorMessage = "This field is required";
    }

    // Email validation
    if (isValid && type === "email" && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = "Please enter a valid email address";
        }
    }

    // Phone validation
    if (isValid && type === "tel" && value) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = "Please enter a valid phone number";
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

    // Pattern validation
    if (isValid && field.pattern && value) {
        const regex = new RegExp(field.pattern);
        if (!regex.test(value)) {
            isValid = false;
            errorMessage = field.dataset.patternMessage || "Invalid format";
        }
    }

    // Update UI
    updateFieldValidationState(field, isValid, errorMessage);

    return isValid;
}

/**
 * Update field validation state
 */
function updateFieldValidationState(field, isValid, errorMessage) {
    const errorEl =
        field.parentNode.querySelector(".error-message") ||
        createErrorMessage(field);

    if (isValid) {
        field.classList.remove("error");
        field.removeAttribute("aria-invalid");
        errorEl.textContent = "";
        errorEl.style.display = "none";
    } else {
        field.classList.add("error");
        field.setAttribute("aria-invalid", "true");
        errorEl.textContent = errorMessage;
        errorEl.style.display = "block";
    }
}

/**
 * Create error message element
 */
function createErrorMessage(field) {
    const errorEl = document.createElement("p");
    errorEl.className = "error-message text-red-500 text-sm mt-1";
    errorEl.setAttribute("role", "alert");
    field.parentNode.appendChild(errorEl);
    return errorEl;
}

/* ==================== COPY TO CLIPBOARD ==================== */

/**
 * Initialize copy to clipboard
 */
function initCopyToClipboard() {
    document.querySelectorAll("[data-copy]").forEach((btn) => {
        btn.addEventListener("click", function () {
            const text = this.dataset.copy;
            copyToClipboard(text);
        });
    });
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification("Copied to clipboard!", "success");
        });
    } else {
        // Fallback
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        textarea.style.opacity = "0";
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);
        showNotification("Copied to clipboard!", "success");
    }
}

/* ==================== DROPDOWNS ==================== */

/**
 * Initialize dropdowns
 */
function initDropdowns() {
    document.querySelectorAll("[data-dropdown-toggle]").forEach((toggle) => {
        toggle.addEventListener("click", function (e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;

            // Close other dropdowns
            document.querySelectorAll("[data-dropdown].open").forEach((d) => {
                if (d !== dropdown) {
                    d.classList.remove("open");
                }
            });

            dropdown.classList.toggle("open");
        });
    });

    // Close on outside click
    document.addEventListener("click", function () {
        document
            .querySelectorAll("[data-dropdown].open")
            .forEach((dropdown) => {
                dropdown.classList.remove("open");
            });
    });
}

/* ==================== TABS ==================== */

/**
 * Initialize tabs
 */
function initTabs() {
    document.querySelectorAll("[data-tabs]").forEach((tabContainer) => {
        const tabs = tabContainer.querySelectorAll("[role='tab']");
        const panels = tabContainer.querySelectorAll("[role='tabpanel']");

        tabs.forEach((tab) => {
            tab.addEventListener("click", function () {
                // Deactivate all tabs
                tabs.forEach((t) => {
                    t.setAttribute("aria-selected", "false");
                    t.classList.remove("active");
                });

                // Hide all panels
                panels.forEach((p) => {
                    p.classList.add("hidden");
                });

                // Activate clicked tab
                this.setAttribute("aria-selected", "true");
                this.classList.add("active");

                // Show corresponding panel
                const panelId = this.getAttribute("aria-controls");
                const panel = document.getElementById(panelId);
                if (panel) {
                    panel.classList.remove("hidden");
                }
            });

            // Keyboard navigation
            tab.addEventListener("keydown", function (e) {
                const tabList = Array.from(tabs);
                const index = tabList.indexOf(this);

                switch (e.key) {
                    case "ArrowRight":
                        e.preventDefault();
                        tabList[(index + 1) % tabList.length].focus();
                        break;
                    case "ArrowLeft":
                        e.preventDefault();
                        tabList[
                            (index - 1 + tabList.length) % tabList.length
                        ].focus();
                        break;
                    case "Home":
                        e.preventDefault();
                        tabList[0].focus();
                        break;
                    case "End":
                        e.preventDefault();
                        tabList[tabList.length - 1].focus();
                        break;
                }
            });
        });
    });
}

/* ==================== ACCORDIONS ==================== */

/**
 * Initialize accordions
 */
function initAccordions() {
    document.querySelectorAll("[data-accordion]").forEach((accordion) => {
        const buttons = accordion.querySelectorAll("[aria-expanded]");

        buttons.forEach((button) => {
            button.addEventListener("click", function () {
                const isExpanded =
                    this.getAttribute("aria-expanded") === "true";
                const panel = document.getElementById(
                    this.getAttribute("aria-controls"),
                );

                // Toggle current
                this.setAttribute("aria-expanded", !isExpanded);
                panel.classList.toggle("hidden", isExpanded);

                // Close others if single mode
                if (accordion.dataset.accordion === "single") {
                    buttons.forEach((btn) => {
                        if (btn !== this) {
                            btn.setAttribute("aria-expanded", "false");
                            const otherPanel = document.getElementById(
                                btn.getAttribute("aria-controls"),
                            );
                            if (otherPanel) {
                                otherPanel.classList.add("hidden");
                            }
                        }
                    });
                }
            });
        });
    });
}

/* ==================== SEARCH ==================== */

/**
 * Initialize search
 */
function initSearch() {
    const searchInputs = document.querySelectorAll("[data-search]");

    searchInputs.forEach((input) => {
        const target = document.querySelector(input.dataset.search);
        if (!target) return;

        input.addEventListener(
            "input",
            debounce(function () {
                const term = this.value.toLowerCase();
                const items = target.querySelectorAll("[data-search-item]");

                items.forEach((item) => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(term) ? "" : "none";
                });
            }, 300),
        );
    });
}

/* ==================== UTILITY FUNCTIONS ==================== */

/**
 * Show notification (toast)
 */
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `toast-notification toast-${type} toast-enter`;
    notification.textContent = message;
    notification.setAttribute("role", "alert");
    notification.setAttribute("aria-live", "polite");

    document.body.appendChild(notification);

    // Trigger animation
    setTimeout(() => notification.classList.add("show"), 10);

    // Remove after delay
    setTimeout(() => {
        notification.classList.remove("show");
        notification.classList.add("toast-leave");
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Announce to screen reader
 */
function announceToScreenReader(message) {
    const announcement = document.createElement("div");
    announcement.setAttribute("role", "status");
    announcement.setAttribute("aria-live", "polite");
    announcement.setAttribute("aria-atomic", "true");
    announcement.className = "sr-only";
    announcement.textContent = message;

    document.body.appendChild(announcement);

    setTimeout(() => {
        document.body.removeChild(announcement);
    }, 1000);
}

/**
 * Format date
 */
function formatDate(date, format = "default") {
    const d = new Date(date);
    const options = {
        default: { year: "numeric", month: "short", day: "numeric" },
        long: { year: "numeric", month: "long", day: "numeric" },
        short: { month: "short", day: "numeric" },
        time: { hour: "2-digit", minute: "2-digit" },
        datetime: {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        },
    };

    return d.toLocaleDateString("en-US", options[format] || options.default);
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = "USD") {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: currency,
    }).format(amount);
}

/**
 * Debounce function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit = 100) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

/**
 * Export to CSV
 */
function exportToCSV(tableId, filename = "export.csv") {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll("tr");

    rows.forEach((row) => {
        const cols = row.querySelectorAll("td, th");
        const rowData = [];
        cols.forEach((col) => {
            rowData.push(`"${col.textContent.trim()}"`);
        });
        csv.push(rowData.join(","));
    });

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

/**
 * Confirm action
 */
function confirmAction(message = "Are you sure?") {
    return confirm(message);
}

// Make functions available globally
window.copyToClipboard = copyToClipboard;
window.showNotification = showNotification;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.debounce = debounce;
window.throttle = throttle;
window.confirmAction = confirmAction;
window.exportToCSV = exportToCSV;
window.announceToScreenReader = announceToScreenReader;
window.openModal = openModal;
window.closeModal = closeModal;
window.toggleDarkMode = toggleDarkMode;
