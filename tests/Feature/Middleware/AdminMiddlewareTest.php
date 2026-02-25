<?php

use App\Models\User;

it('allows admin to access admin dashboard', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/admin-dashboard');

    $response->assertStatus(200);
});

it('allows super admin to access admin dashboard', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->get('/admin-dashboard');

    $response->assertStatus(200);
});

it('denies logger access to admin dashboard', function () {
    $user = User::factory()->logger()->create();

    $response = $this->actingAs($user)
        ->from('/dashboard')
        ->get('/admin-dashboard');

    $response->assertRedirect('/dashboard');
});

it('redirects guest to login from admin dashboard', function () {
    $response = $this->get('/admin-dashboard');

    $response->assertRedirect(route('home'));
});

it('allows admin to access student list', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/student-list');

    $response->assertStatus(200);
});

it('allows admin to access student logs', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/student-logs');

    $response->assertStatus(200);
});

it('denies logger access to student list', function () {
    $user = User::factory()->logger()->create();

    $response = $this->actingAs($user)
        ->from('/dashboard')
        ->get('/student-list');

    $response->assertRedirect('/dashboard');
});
