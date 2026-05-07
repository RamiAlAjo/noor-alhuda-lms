<?php

use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    // Create or find test user
    $user = User::where('email', 'ramialajo@outlook.com')->first();

    if (! $user) {
        $user = User::create([
            'email' => 'ramialajo@outlook.com',
            'password' => Hash::make('Test123!'),
            'name' => 'Rami Test',
            'user_id' => 'TEST-001',
        ]);

        $user->profile()->create([
            'first_name' => 'Rami',
            'last_name' => 'Alajo',
        ]);

        $user->assignRole('student');
    }

    // Send test email
    try {
        Mail::to('ramialajo@outlook.com')->send(new UserCredentials($user, 'Test123!'));

        return 'Email sent successfully to ramialajo@outlook.com! Check your Laravel log file.';
    } catch (\Exception $e) {
        return 'Email error: '.$e->getMessage();
    }
});
