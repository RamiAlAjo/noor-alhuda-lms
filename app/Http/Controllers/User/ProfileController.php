<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['profile.department', 'profile.major']);

        return view('pages.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load(['profile.department', 'profile.major']);

        return view('pages.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate profile data - all fields are optional for updates
        $rules = [
            'email' => ['nullable', 'email', 'unique:users,email,'.$user->id],
            'first_name' => ['nullable', 'string', 'max:50'],
            'second_name' => ['nullable', 'string', 'max:50'],
            'third_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'personal_email' => ['nullable', 'email'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            // Emergency contact
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            // Social links
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
        ];

        // Add role-specific validation
        if ($user->isTeacher()) {
            $rules['bio'] = ['nullable', 'string', 'max:2000'];
            $rules['cv'] = ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'];
        }

        $request->validate($rules);

        // Update user email if provided
        if ($request->filled('email')) {
            $user->update([
                'email' => $request->email,
            ]);
        }

        // Update user name if both first_name and last_name are provided and not empty
        if ($request->filled('first_name') && $request->filled('last_name')) {
            $user->update([
                'name' => $request->first_name.' '.$request->last_name,
            ]);
        } elseif ($request->filled('first_name') && ! $request->filled('last_name')) {
            // If only first_name is provided, keep existing last_name
            $user->update([
                'name' => $request->first_name.' '.($user->profile?->last_name ?? ''),
            ]);
        } elseif (! $request->filled('first_name') && $request->filled('last_name')) {
            // If only last_name is provided, keep existing first_name
            $user->update([
                'name' => ($user->profile?->first_name ?? $user->name).' '.$request->last_name,
            ]);
        }

        // Build profile data - include fields that have values in request
        $profileData = [];

        if ($request->filled('first_name')) {
            $profileData['first_name'] = $request->first_name;
        }
        if ($request->filled('second_name')) {
            $profileData['second_name'] = $request->second_name;
        }
        if ($request->filled('third_name')) {
            $profileData['third_name'] = $request->third_name;
        }
        if ($request->filled('last_name')) {
            $profileData['last_name'] = $request->last_name;
        }
        if ($request->filled('phone')) {
            $profileData['phone'] = $request->phone;
        }
        if ($request->filled('nationality')) {
            $profileData['nationality'] = $request->nationality;
        }
        if ($request->filled('personal_email')) {
            $profileData['personal_email'] = $request->personal_email;
        }
        if ($request->filled('address')) {
            $profileData['address'] = $request->address;
        }
        if ($request->filled('city')) {
            $profileData['city'] = $request->city;
        }
        if ($request->filled('country')) {
            $profileData['country'] = $request->country;
        }
        if ($request->filled('postal_code')) {
            $profileData['postal_code'] = $request->postal_code;
        }
        if ($request->filled('date_of_birth')) {
            $profileData['date_of_birth'] = $request->date_of_birth;
        }
        if ($request->filled('gender')) {
            $profileData['gender'] = $request->gender;
        }
        if ($request->filled('emergency_contact_name')) {
            $profileData['emergency_contact_name'] = $request->emergency_contact_name;
        }
        if ($request->filled('emergency_contact_relationship')) {
            $profileData['emergency_contact_relationship'] = $request->emergency_contact_relationship;
        }
        if ($request->filled('emergency_phone')) {
            $profileData['emergency_phone'] = $request->emergency_phone;
        }

        // Handle social links
        $socialLinks = [];
        if ($request->filled('facebook')) {
            $socialLinks['facebook'] = $request->facebook;
        }
        if ($request->filled('twitter')) {
            $socialLinks['twitter'] = $request->twitter;
        }
        if ($request->filled('linkedin')) {
            $socialLinks['linkedin'] = $request->linkedin;
        }
        if ($request->filled('instagram')) {
            $socialLinks['instagram'] = $request->instagram;
        }

        if (! empty($socialLinks)) {
            $profileData['social_links'] = $socialLinks;
        }

        // Add role-specific fields
        if ($user->isTeacher()) {
            if ($request->filled('bio')) {
                $profileData['bio'] = $request->bio;
            }

            // Handle CV upload
            if ($request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('cvs', 'public');
                $profileData['cv'] = $cvPath;
            }
        }

        // Update or create profile only if there's data to update
        if (! empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return redirect()->route('profile.show')
            ->with('success', __('lms::messages.profile_updated'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Check current password
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('lms::messages.current_password_incorrect')]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', __('lms::messages.password_updated'));
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        // Store the photo
        $path = $request->file('photo')->store('profile-photos', 'public');

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['photo' => $path]
        );

        return back()->with('success', __('lms::messages.photo_updated'));
    }
}
