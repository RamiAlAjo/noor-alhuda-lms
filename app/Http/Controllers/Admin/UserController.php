<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // Use select to fetch only needed columns for better performance
        $query = User::select(['id', 'user_id', 'email', 'name', 'is_active', 'status', 'created_at'])
            ->with(['roles', 'profile', 'major.department']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($pq) use ($search) {
                        $pq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('role') && $request->role) {
            $query->role($request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Cache roles for 1 hour (static data)
        $roles = Cache::remember('all_roles', now()->addHour(), function () {
            return Role::all();
        });

        return view('pages.admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Cache roles for 1 hour (static data)
        $roles = Cache::remember('all_roles', now()->addHour(), function () {
            return Role::all();
        });

        $departments = \App\Models\Department::all();
        $majors = \App\Models\Major::all();

        return view('pages.admin.users.create', compact('roles', 'departments', 'majors'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $role = $request->role;

        // Build validation rules based on role
        $rules = [
            'email' => $request->auto_generate_email
                ? ['nullable', 'email']
                : ['required', 'email', 'unique:users'],
            'password' => $request->auto_generate_password
                ? ['nullable', 'string', 'min:8']
                : ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'first_name' => ['required', 'string', 'max:50'],
            'second_name' => ['nullable', 'string', 'max:50'],
            'third_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'auto_generate_email' => ['nullable', 'boolean'],
            'auto_generate_id' => ['nullable', 'boolean'],
            'auto_generate_password' => ['nullable', 'boolean'],
            // New fields
            'nationality' => ['nullable', 'string', 'max:100'],
            'personal_email' => ['nullable', 'email'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ];

        // Role-specific validation
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if ($role === 'teacher') {
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
            $rules['blood_type'] = ['nullable', Rule::in($bloodTypes)];
            $rules['years_of_experience'] = ['nullable', 'integer', 'min:0', 'max:50'];
            $rules['bio'] = ['nullable', 'string', 'max:2000'];
        } elseif ($role === 'student') {
            $rules['major_id'] = ['nullable', 'exists:majors,id'];
            $rules['blood_type'] = ['nullable', Rule::in($bloodTypes)];
            $rules['emergency_phone'] = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules);

        // Auto-generate user ID based on role
        $userId = $request->auto_generate_id
            ? User::generateUserId($role)
            : $request->user_id;

        // Auto-generate email based on name if requested
        $email = $request->auto_generate_email
            ? User::generateEmail($request->first_name, $request->last_name)
            : $request->email;

        // Auto-generate password if requested
        $password = null;
        if ($request->auto_generate_password) {
            $password = Str::random(12);
        } elseif ($request->filled('password')) {
            $password = $request->password;
        }

        // Check if email is unique
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => __('lms::messages.email_exists')])->withInput();
        }

        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
            'name' => $request->first_name.' '.$request->last_name,
            'user_id' => $userId,
        ]);

        // Build profile data
        $profileData = [
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'third_name' => $request->third_name,
            'last_name' => $request->last_name,
            'role_specific_id' => $userId,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'personal_email' => $request->personal_email,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ];

        // Store initial password if auto-generated
        if ($password && $request->auto_generate_password) {
            $profileData['initial_password'] = $password;
        }

        // Add role-specific profile fields
        if ($role === 'teacher') {
            $profileData['department_id'] = $request->department_id;
            $profileData['blood_type'] = $request->blood_type;
            $profileData['years_of_experience'] = $request->years_of_experience;
            $profileData['bio'] = $request->bio;
            $profileData['office_hours'] = $request->office_hours ? json_decode($request->office_hours, true) : null;
        } elseif ($role === 'student') {
            $profileData['major_id'] = $request->major_id;
            $profileData['blood_type'] = $request->blood_type;
            $profileData['emergency_phone'] = $request->emergency_phone;
        }

        // Create user profile
        $user->profile()->create($profileData);

        // Assign role
        $user->assignRole($role);

        // Clear dashboard stats cache
        Cache::forget('admin_dashboard_stats');

        // Prepare success message
        $message = __('lms::messages.user_created');
        if ($password && $request->auto_generate_password) {
            $message .= ' '.__('lms::messages.password_generated', ['password' => $password]);
            // Store password in session for display on show page
            session()->flash('generated_password', $password);
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', $message);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load([
            'roles',
            'profile.department',
            'profile.major',
            'major.department',
            'enrollments.offering.course',
            'enrollments.offering.semester',
            'taughtCourses.course.department',
            'notes',
        ]);

        return view('pages.admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load(['roles', 'profile', 'major']);

        // Cache roles for 1 hour (static data)
        $roles = Cache::remember('all_roles', now()->addHour(), function () {
            return Role::all();
        });

        $departments = \App\Models\Department::all();
        $majors = \App\Models\Major::all();

        return view('pages.admin.users.edit', compact('user', 'roles', 'departments', 'majors'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $role = $request->role ?? $user->roles()->first()?->name;

        // Build validation rules based on role
        $rules = [
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'first_name' => ['required', 'string', 'max:50'],
            'second_name' => ['nullable', 'string', 'max:50'],
            'third_name' => ['nullable', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            // New fields
            'nationality' => ['nullable', 'string', 'max:100'],
            'personal_email' => ['nullable', 'email'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ];

        // Role-specific validation
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        if ($role === 'teacher') {
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
            $rules['blood_type'] = ['nullable', Rule::in($bloodTypes)];
            $rules['years_of_experience'] = ['nullable', 'integer', 'min:0', 'max:50'];
            $rules['bio'] = ['nullable', 'string', 'max:2000'];
        } elseif ($role === 'student') {
            $rules['major_id'] = ['nullable', 'exists:majors,id'];
            $rules['blood_type'] = ['nullable', Rule::in($bloodTypes)];
            $rules['emergency_phone'] = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules);

        $user->update([
            'email' => $request->email,
            'name' => $request->first_name.' '.$request->last_name,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Build profile update data
        $profileData = [
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'third_name' => $request->third_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'personal_email' => $request->personal_email,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
        ];

        // Add role-specific profile fields
        if ($role === 'teacher') {
            $profileData['department_id'] = $request->department_id;
            $profileData['blood_type'] = $request->blood_type;
            $profileData['years_of_experience'] = $request->years_of_experience;
            $profileData['bio'] = $request->bio;
            $profileData['office_hours'] = $request->office_hours ? json_decode($request->office_hours, true) : null;
        } elseif ($role === 'student') {
            $profileData['major_id'] = $request->major_id;
            $profileData['blood_type'] = $request->blood_type;
            $profileData['emergency_phone'] = $request->emergency_phone;
        }

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Update role
        $user->syncRoles([$request->role]);

        // Clear dashboard stats cache
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.index')
            ->with('success', __('lms::messages.user_updated'));
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', __('lms::messages.cannot_delete_self'));
        }

        $user->delete();

        // Clear dashboard stats cache
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.index')
            ->with('success', __('lms::messages.user_deleted'));
    }

    /**
     * Show bulk import form.
     */
    public function import()
    {
        // Cache roles for 1 hour (static data)
        $roles = Cache::remember('all_roles', now()->addHour(), function () {
            return Role::all();
        });

        return view('pages.admin.users.import', compact('roles'));
    }

    /**
     * Process bulk import from CSV/Excel.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120',
            'role' => 'required|exists:roles,name',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if ($extension === 'csv') {
            $handle = fopen($file->path(), 'r');
            $headers = fgetcsv($handle);

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    $data = array_combine($headers, $row);

                    try {
                        // Check if email exists
                        if (User::where('email', $data['email'] ?? '')->exists()) {
                            $results['failed']++;
                            $results['errors'][] = "Email {$data['email']} already exists";

                            continue;
                        }

                        // Generate user_id
                        $userId = User::generateUserId($request->role);

                        $user = User::create([
                            'email' => $data['email'],
                            'password' => Hash::make($data['password'] ?? 'password123'),
                            'name' => ($data['first_name'] ?? '').' '.($data['last_name'] ?? ''),
                            'user_id' => $userId,
                        ]);

                        // Build profile data
                        $profileData = [
                            'first_name' => $data['first_name'] ?? '',
                            'second_name' => $data['second_name'] ?? '',
                            'third_name' => $data['third_name'] ?? '',
                            'last_name' => $data['last_name'] ?? '',
                            'family_name' => $data['family_name'] ?? '',
                            'role_specific_id' => $userId,
                            'phone' => $data['phone'] ?? null,
                            'gender' => $data['gender'] ?? null,
                            'date_of_birth' => $data['date_of_birth'] ?? null,
                            'nationality' => $data['nationality'] ?? null,
                            'personal_email' => $data['personal_email'] ?? null,
                            'address' => $data['address'] ?? null,
                            'city' => $data['city'] ?? null,
                            'country' => $data['country'] ?? null,
                            'postal_code' => $data['postal_code'] ?? null,
                            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                            'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
                        ];

                        // Add role-specific fields if present in CSV
                        if ($request->role === 'teacher') {
                            if (isset($data['department_id'])) {
                                $profileData['department_id'] = $data['department_id'];
                            }
                            if (isset($data['blood_type'])) {
                                $profileData['blood_type'] = $data['blood_type'];
                            }
                            if (isset($data['years_of_experience'])) {
                                $profileData['years_of_experience'] = $data['years_of_experience'];
                            }
                            if (isset($data['bio'])) {
                                $profileData['bio'] = $data['bio'];
                            }
                        } elseif ($request->role === 'student') {
                            if (isset($data['major_id'])) {
                                $profileData['major_id'] = $data['major_id'];
                            }
                            if (isset($data['emergency_phone'])) {
                                $profileData['emergency_phone'] = $data['emergency_phone'];
                            }
                            if (isset($data['blood_type'])) {
                                $profileData['blood_type'] = $data['blood_type'];
                            }
                        }

                        $user->profile()->create($profileData);

                        $user->assignRole($request->role);
                        $results['success']++;
                    } catch (\Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = "Error importing {$data['email']}: ".$e->getMessage();
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);

                return back()->with('error', 'Import failed: '.$e->getMessage());
            }

            fclose($handle);
        }

        // Clear dashboard stats cache
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.index')
            ->with('success', "Import completed: {$results['success']} users imported, {$results['failed']} failed");
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $users = User::with(['profile', 'roles', 'major.department'])->get();

        $filename = 'users_export_'.date('Y_m_d_H_i_s').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'user_id', 'email', 'first_name', 'middle_name', 'last_name', 'family_name',
                'phone', 'gender', 'date_of_birth', 'role',
                'nationality', 'personal_email', 'address', 'city', 'country', 'postal_code',
                'emergency_contact_name', 'emergency_contact_relationship',
                'department', 'major', 'blood_type', 'years_of_experience',
            ]);

            foreach ($users as $user) {
                $role = $user->roles->first()?->name ?? '';

                fputcsv($handle, [
                    $user->user_id,
                    $user->email,
                    $user->profile->first_name ?? '',
                    $user->profile->middle_name ?? '',
                    $user->profile->last_name ?? '',
                    $user->profile->family_name ?? '',
                    $user->profile->phone ?? '',
                    $user->profile->gender ?? '',
                    $user->profile->date_of_birth ?? '',
                    $role,
                    $user->profile->nationality ?? '',
                    $user->profile->personal_email ?? '',
                    $user->profile->address ?? '',
                    $user->profile->city ?? '',
                    $user->profile->country ?? '',
                    $user->profile->postal_code ?? '',
                    $user->profile->emergency_contact_name ?? '',
                    $user->profile->emergency_contact_relationship ?? '',
                    $user->profile->department?->name ?? ($user->profile->department_id ?? ''),
                    $user->major?->name ?? ($user->profile->major_id ?? ''),
                    $user->profile->blood_type ?? '',
                    $user->profile->years_of_experience ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Activate a user account.
     */
    public function activate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('lms.cannot_activate_self'));
        }

        $user->update(['is_active' => true]);

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms.user_activated'));
    }

    /**
     * Deactivate a user account.
     */
    public function deactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('lms.cannot_deactivate_self'));
        }

        $user->update(['is_active' => false]);

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms.user_deactivated'));
    }

    /**
     * Bulk activate users.
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = array_diff($request->user_ids, [auth()->id()]);

        User::whereIn('id', $userIds)->update(['is_active' => true]);

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms.users_bulk_activated', ['count' => count($userIds)]));
    }

    /**
     * Bulk deactivate users.
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = array_diff($request->user_ids, [auth()->id()]);

        User::whereIn('id', $userIds)->update(['is_active' => false]);

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms.users_bulk_deactivated', ['count' => count($userIds)]));
    }

    /**
     * Bulk delete users.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = array_diff($request->user_ids, [auth()->id()]);

        User::whereIn('id', $userIds)->delete();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', __('lms.users_bulk_deleted', ['count' => count($userIds)]));
    }

    /**
     * Generate preview email based on name.
     */
    public function previewEmail(Request $request)
    {
        $firstName = $request->first_name;
        $lastName = $request->last_name;

        if (! $firstName || ! $lastName) {
            return response()->json(['error' => 'First name and last name are required'], 400);
        }

        $email = User::generateEmail($firstName, $lastName);

        return response()->json(['email' => $email]);
    }

    /**
     * Generate preview ID based on role.
     */
    public function previewId(Request $request)
    {
        $role = $request->role;

        if (! $role) {
            return response()->json(['error' => 'Role is required'], 400);
        }

        $userId = User::generateUserId($role);

        return response()->json(['user_id' => $userId]);
    }

    /**
     * Send credentials email to user.
     */
    public function sendCredentials(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        try {
            Mail::to($user->email)->send(new UserCredentials($user, $request->password));

            return back()->with('success', __('lms::messages.credentials_email_sent'));
        } catch (\Exception $e) {
            return back()->with('error', __('lms::messages.credentials_email_failed'));
        }
    }
}
