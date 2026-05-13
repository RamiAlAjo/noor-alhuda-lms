<?php

use App\Models\User;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->put(route('profile.update'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect(route('profile.show'));

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});
