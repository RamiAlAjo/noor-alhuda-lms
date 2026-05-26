<div
    x-data="{
        isOpen: <?php if ((object) ('isOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'->value()); ?>')<?php echo e('isOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'); ?>')<?php endif; ?>,
        soundEnabled: <?php if ((object) ('soundEnabled') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('soundEnabled'->value()); ?>')<?php echo e('soundEnabled'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('soundEnabled'); ?>')<?php endif; ?>,
        pushEnabled: <?php if ((object) ('pushEnabled') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('pushEnabled'->value()); ?>')<?php echo e('pushEnabled'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('pushEnabled'); ?>')<?php endif; ?>,
        showToasts: []
    }"
    x-on:notification-received.window="handleNotification($event)"
    x-on:click.away="isOpen = false"
    x-on:keydown.escape.window="isOpen = false"
    x-init="console.log('Notification dropdown initialized'); $wire.checkForNewNotifications(); setInterval(() => { console.log('Checking for new notifications...'); $wire.checkForNewNotifications(); }, 10000)"
    class="relative"
>
    <!-- Notification Bell Button -->
    <?php if (isset($component)) { $__componentOriginalf5109f209df079b3a83484e1e6310749 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf5109f209df079b3a83484e1e6310749 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::tooltip.index','data' => ['content' => __('Notifications'),'position' => 'bottom']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['content' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Notifications')),'position' => 'bottom']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <button
            type="button"
            x-on:click="isOpen = !isOpen"
            x-bind:aria-expanded="isOpen"
            aria-controls="notifications-dropdown"
            class="relative !h-10 navbar-icon-btn flex items-center justify-center rounded-lg transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:ring-offset-2"
            aria-label="<?php echo e(__('Notifications')); ?>"
            aria-haspopup="true"
            role="button"
        >
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bell','class' => 'size-5 text-zinc-600 dark:text-zinc-400','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell','class' => 'size-5 text-zinc-600 dark:text-zinc-400','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>

            <!-- Unread Count Badge -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <span
                    class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1 animate-pulse"
                    x-show="<?php echo e($unreadCount); ?> > 0"
                    aria-label="<?php echo e(__(':count unread notifications', ['count' => $unreadCount])); ?>"
                >
                    <?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf5109f209df079b3a83484e1e6310749)): ?>
<?php $attributes = $__attributesOriginalf5109f209df079b3a83484e1e6310749; ?>
<?php unset($__attributesOriginalf5109f209df079b3a83484e1e6310749); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf5109f209df079b3a83484e1e6310749)): ?>
<?php $component = $__componentOriginalf5109f209df079b3a83484e1e6310749; ?>
<?php unset($__componentOriginalf5109f209df079b3a83484e1e6310749); ?>
<?php endif; ?>

    <!-- Dropdown Panel -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        x-trap.noscroll="isOpen"
        class="absolute end-0 mt-2 w-80 sm:w-96 rounded-xl shadow-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 z-50 overflow-hidden"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="notifications-menu"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60">
            <div class="flex items-center justify-between gap-3">
                <!-- Title -->
                <div class="min-w-0">
                    <h2 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100" id="notifications-menu">
                        <?php echo e(__('Notifications')); ?>

                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400" aria-live="polite">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                            <?php echo e($unreadCount); ?> <?php echo e(__('unread')); ?>

                        <?php else: ?>
                            <?php echo e(__('All caught up!')); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center gap-1.5">
                    <!-- Mark all as read -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                        <button
                            type="button"
                            wire:click="markAllAsRead"
                            class="px-2.5 py-1 text-xs font-medium rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] hover:bg-[var(--color-accent)]/15 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        >
                            <?php echo e(__('Mark all read')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Sound Toggle -->
                    <button
                        type="button"
                        x-on:click="$wire.toggleSound()"
                        class="p-1.5 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        x-bind:title="soundEnabled ? '<?php echo e(__('Disable sound')); ?>' : '<?php echo e(__('Enable sound')); ?>'"
                        x-bind:aria-label="soundEnabled ? '<?php echo e(__('Disable notification sound')); ?>' : '<?php echo e(__('Enable notification sound')); ?>'"
                        x-bind:aria-pressed="soundEnabled"
                    >
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'speaker-wave','class' => 'size-4 text-zinc-500','xShow' => 'soundEnabled','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'speaker-wave','class' => 'size-4 text-zinc-500','x-show' => 'soundEnabled','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'speaker-x-mark','class' => 'size-4 text-zinc-500','xShow' => '!soundEnabled','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'speaker-x-mark','class' => 'size-4 text-zinc-500','x-show' => '!soundEnabled','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    </button>

                    <!-- Push Toggle -->
                    <button
                        type="button"
                        x-on:click="$wire.togglePush()"
                        class="p-1.5 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        x-bind:title="pushEnabled ? '<?php echo e(__('Disable push')); ?>' : '<?php echo e(__('Enable push')); ?>'"
                        x-bind:aria-label="pushEnabled ? '<?php echo e(__('Disable push notifications')); ?>' : '<?php echo e(__('Enable push notifications')); ?>'"
                        x-bind:aria-pressed="pushEnabled"
                    >
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bell-alert','class' => 'size-4 text-zinc-500','xShow' => 'pushEnabled','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell-alert','class' => 'size-4 text-zinc-500','x-show' => 'pushEnabled','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bell-slash','class' => 'size-4 text-zinc-500','xShow' => '!pushEnabled','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell-slash','class' => 'size-4 text-zinc-500','x-show' => '!pushEnabled','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 space-y-3">
            <!-- Search -->
            <div class="relative">
                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live.debounce.300ms' => 'searchTerm','placeholder' => ''.e(__('Search notifications...')).'','class' => 'w-full pl-9 text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'searchTerm','placeholder' => ''.e(__('Search notifications...')).'','class' => 'w-full pl-9 text-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-2">
                <select
                    wire:model.live="filterType"
                    class="flex-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 px-3 py-1.5 focus:ring-2 focus:ring-[var(--color-accent)]"
                >
                    <option value="all"><?php echo e(__('All Types')); ?></option>
                    <option value="grade"><?php echo e(__('Grades')); ?></option>
                    <option value="enrollment"><?php echo e(__('Enrollment')); ?></option>
                    <option value="payment"><?php echo e(__('Payments')); ?></option>
                    <option value="announcement"><?php echo e(__('Announcements')); ?></option>
                    <option value="reminder"><?php echo e(__('Reminders')); ?></option>
                    <option value="system"><?php echo e(__('System')); ?></option>
                </select>

                <label class="flex items-center gap-1.5 text-sm whitespace-nowrap px-2 py-1 rounded-lg border border-zinc-300 dark:border-zinc-600 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="showUnreadOnly"
                        class="rounded border-zinc-300 dark:border-zinc-600 text-[var(--color-accent)] focus:ring-[var(--color-accent)]"
                    />
                    <span class="text-xs"><?php echo e(__('Unread only')); ?></span>
                </label>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-[340px] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-600" role="list" aria-label="<?php echo e(__('Notifications list')); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div
                    class="group relative px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors <?php echo e(!$notification['is_read'] ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''); ?>"
                    role="listitem"
                    x-data="{ showDelete: false }"
                    x-on:mouseenter="showDelete = true"
                    x-on:mouseleave="showDelete = false"
                    x-on:focusin="showDelete = true"
                    x-on:focusout="showDelete = false"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <?php
                            $color = in_array($notification['color'] ?? 'slate', ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'])
                                ? $notification['color'] ?? 'slate'
                                : 'slate';
                        ?>
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-<?php echo e($color); ?>-100 dark:bg-<?php echo e($color); ?>-900/30 flex items-center justify-center" aria-hidden="true">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($notification['icon'] ?? 'bell').'','class' => 'size-4 text-'.e($color).'-600 dark:text-'.e($color).'-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($notification['icon'] ?? 'bell').'','class' => 'size-4 text-'.e($color).'-600 dark:text-'.e($color).'-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <a
                                href="<?php echo e($notification['link'] ?? '#'); ?>"
                                x-on:click="$wire.markAsRead(<?php echo e($notification['id']); ?>)"
                                class="block focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded"
                            >
                                <p class="text-sm font-medium <?php echo e(!$notification['is_read'] ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400'); ?>">
                                    <?php echo e($notification['title']); ?>

                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-0.5">
                                    <?php echo e(Str::limit($notification['content'], 80)); ?>

                                </p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                    <time datetime="<?php echo e($notification['created_at']); ?>"><?php echo e($notification['created_at']); ?></time>
                                </p>
                            </a>
                        </div>

                        <!-- Unread Indicator & Actions -->
                        <div class="flex items-center gap-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$notification['is_read']): ?>
                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0" aria-label="<?php echo e(__('Unread')); ?>" title="<?php echo e(__('Unread notification')); ?>"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Delete Button -->
                            <button
                                type="button"
                                x-on:click.stop="$wire.deleteNotification(<?php echo e($notification['id']); ?>)"
                                x-show="showDelete"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                                aria-label="<?php echo e(__('Delete notification: :title', ['title' => $notification['title']])); ?>"
                            >
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-3.5 text-zinc-400','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-3.5 text-zinc-400','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="py-12 text-center" role="status">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bell-slash','class' => 'size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell-slash','class' => 'size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400"><?php echo e(__('No notifications yet')); ?></p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1"><?php echo e(__('We\'ll notify you when something arrives')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Footer -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($notifications) > 0): ?>
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                <a
                    href="<?php echo e(route('notifications.index')); ?>"
                    class="flex items-center justify-center gap-2 text-sm text-[var(--color-accent)] hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded"
                >
                    <?php echo e(__('View all notifications')); ?>

                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-right','class' => 'size-4','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-4','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Toast Notifications -->
    <div
        x-show="showToasts.length > 0"
        class="fixed top-4 right-4 z-[100] space-y-2"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <template x-for="(toast, index) in showToasts" :key="index">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 max-w-sm"
                role="alert"
            >
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[var(--color-accent)]/10 flex items-center justify-center" aria-hidden="true">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bell','class' => 'size-4 text-[var(--color-accent)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bell','class' => 'size-4 text-[var(--color-accent)]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="toast.title"></p>
                </div>
                <button
                    type="button"
                    x-on:click="toast.visible = false"
                    class="flex-shrink-0 p-1 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                    aria-label="<?php echo e(__('Dismiss notification')); ?>"
                >
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-4 text-zinc-400','ariaHidden' => 'true']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-4 text-zinc-400','aria-hidden' => 'true']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                </button>
            </div>
        </template>
    </div>

    <!-- Notification Sound (Web Audio API fallback) -->
    <div id="notificationSound" style="display: none;"></div>

    <script>
        let pushSubscription = null;

        // Register service worker for push notifications
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration.scope);

                    // Check if push is supported
                    if ('PushManager' in window) {
                        registration.pushManager.getSubscription()
                            .then(function(subscription) {
                                if (subscription) {
                                    pushSubscription = subscription;
                                    console.log('Already subscribed to push notifications');
                                } else {
                                    console.log('Not subscribed to push notifications');
                                }
                            });
                    }
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }

        // Request push notification permission
        async function requestPushPermission() {
            if (!('Notification' in window)) {
                console.log('This browser does not support notifications');
                return false;
            }

            if (Notification.permission === 'granted') {
                return true;
            }

            if (Notification.permission !== 'denied') {
                const permission = await Notification.permission;
                if (permission === 'granted') {
                    return true;
                }
            }

            return false;
        }

        // Show browser notification (fallback for when service worker isn't available)
        function showBrowserNotification(title, options = {}) {
            if (Notification.permission === 'granted') {
                try {
                    const notification = new Notification(title, {
                        body: options.body || 'You have a new notification',
                        icon: options.icon || '/favicon.ico',
                        badge: options.badge || '/favicon.ico',
                        tag: options.tag || 'noor-notification',
                        requireInteraction: options.requireInteraction || false,
                        silent: options.silent || false,
                        ...options
                    });

                    notification.onclick = function() {
                        window.focus();
                        if (options.url) {
                            window.location.href = options.url;
                        }
                        notification.close();
                    };

                    // Auto-close after 5 seconds if not requiring interaction
                    if (!options.requireInteraction) {
                        setTimeout(() => notification.close(), 5000);
                    }
                } catch (e) {
                    console.log('Browser notification failed:', e.message);
                }
            }
        }

        function handleNotification(event) {
            console.log('Notification received:', event.detail);

            const { soundEnabled, title, type, pushEnabled } = event.detail;

            // Play sound if enabled
            if (soundEnabled) {
                console.log('Playing notification sound for type:', type);
                playNotificationSound(type || 'default');
            } else {
                console.log('Sound disabled');
            }

            // Show browser notification if push is enabled
            if (pushEnabled && Notification.permission === 'granted') {
                console.log('Showing browser notification');
                showBrowserNotification(title, {
                    body: 'You have a new notification from Noor LMS',
                    icon: '/favicon.ico',
                    url: window.location.href,
                    tag: `noor-${type}`,
                    requireInteraction: type === 'announcement' || type === 'system'
                });
            }

            // Show toast notification
            this.showToasts.push({
                title: title,
                type: type,
                visible: true
            });

            // Auto-hide toast after 5 seconds
            setTimeout(() => {
                const toast = this.showToasts.find(t => t.title === title);
                if (toast) {
                    toast.visible = false;
                }
            }, 5000);
        }

        // Enhanced Web Audio API notification sounds with different types
        function playNotificationSound(type = 'default') {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                // Different sound patterns for different notification types
                switch (type) {
                    case 'grade':
                        // Success sound - pleasant ascending tone
                        oscillator.frequency.setValueAtTime(523, audioContext.currentTime); // C5
                        oscillator.frequency.setValueAtTime(659, audioContext.currentTime + 0.1); // E5
                        oscillator.frequency.setValueAtTime(784, audioContext.currentTime + 0.2); // G5
                        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.4);
                        break;

                    case 'reminder':
                        // Reminder sound - gentle pulsing
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime); // A4
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime + 0.15);
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime + 0.3);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime);
                        gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.1);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime + 0.15);
                        gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.25);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime + 0.3);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.5);
                        break;

                    case 'announcement':
                        // Announcement sound - clear and attention-grabbing
                        oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
                        oscillator.frequency.setValueAtTime(660, audioContext.currentTime + 0.1); // E5
                        oscillator.frequency.setValueAtTime(880, audioContext.currentTime + 0.2); // A5
                        gainNode.gain.setValueAtTime(0.25, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                        break;

                    default:
                        // Default notification sound
                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                }

                console.log(`Playing ${type} notification sound`);
            } catch (e) {
                console.log('Notification sound not available:', e.message);
                // Fallback: try to play system notification sound if available
                try {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('New Notification', {
                            body: 'You have a new notification',
                            icon: '/favicon.ico',
                            silent: false
                        });
                    }
                } catch (fallbackError) {
                    console.log('Fallback notification failed:', fallbackError.message);
                }
            }
        }
    </script>
</div>
<?php /**PATH C:\Users\Rami\Documents\programming\noor-alhuda-lms\resources\views/livewire/notification-dropdown.blade.php ENDPATH**/ ?>