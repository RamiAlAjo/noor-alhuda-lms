<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use ProfileValidationRules;

    public string $first_name = '';
    public string $second_name = '';
    public string $third_name = '';
    public string $last_name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        // Load profile if not already loaded
        if (!$user->profile) {
            $user->profile()->create([
                'user_id_unique' => User::generateUserId($user->getPrimaryRoleAttribute() ?? 'student'),
            ]);
            $user->refresh();
        }

        $this->first_name = $user->profile?->first_name ?? '';
        $this->second_name = $user->profile?->second_name ?? '';
        $this->third_name = $user->profile?->third_name ?? '';
        $this->last_name = $user->profile?->last_name ?? '';
        $this->email = $user->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        // Update main user email if changed
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }
        $user->save();

        // Update or create profile with four-part name
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $validated['first_name'],
                'second_name' => $validated['second_name'] ?? null,
                'third_name' => $validated['third_name'] ?? null,
                'last_name' => $validated['last_name'],
            ]
        );

        // Update the name field for backward compatibility
        $fullName = trim(implode(' ', array_filter([
            $validated['first_name'],
            $validated['second_name'],
            $validated['third_name'],
            $validated['last_name']
        ])));
        $user->name = $fullName;
        $user->save();

        $this->dispatch('profile-updated', name: $fullName);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="first_name"
                    :label="__('First Name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="given-name"
                    placeholder="{{ __('Enter first name') }}"
                />

                <flux:input
                    wire:model="second_name"
                    :label="__('Second Name')"
                    type="text"
                    autocomplete="additional-name"
                    placeholder="{{ __('Enter second name') }}"
                />

                <flux:input
                    wire:model="third_name"
                    :label="__('Third Name')"
                    type="text"
                    autocomplete="additional-name"
                    placeholder="{{ __('Enter third name') }}"
                />

                <flux:input
                    wire:model="last_name"
                    :label="__('Last Name')"
                    type="text"
                    required
                    autocomplete="family-name"
                    placeholder="{{ __('Enter last name') }}"
                />
            </div>

            <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

            @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-4">
                        {{ __('Your email address is unverified.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
