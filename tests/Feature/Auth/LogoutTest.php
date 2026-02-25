<?php

use App\Models\User;

it('can logout an authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});

it('invalidates session on logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Verify user is authenticated
    $this->assertAuthenticated();

    $this->post('/logout');

    // Verify user is no longer authenticated
    $this->assertGuest();
});
