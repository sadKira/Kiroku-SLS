<?php

use App\Models\User;
use App\Models\LogSession;

it('allows logger to access logger dashboard', function () {
    $user = User::factory()->logger()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
});

it('allows logger to access view-logs page', function () {
    $user = User::factory()->logger()->create();
    $logSession = LogSession::factory()->create();

    $response = $this->actingAs($user)->get('/view-log/' . $logSession->date);

    $response->assertStatus(200);
});

it('denies admin access to logger dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->from('/admin-dashboard')
        ->get('/dashboard');

    $response->assertRedirect('/admin-dashboard');
});

it('denies super admin access to logger dashboard', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)
        ->from('/admin-dashboard')
        ->get('/dashboard');

    $response->assertRedirect('/admin-dashboard');
});

it('redirects guest to login from logger dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect(route('home'));
});
