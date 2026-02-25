<?php

use App\Models\User;
use App\Livewire\Auth\Login;
use Livewire\Livewire;

it('renders login page for guests', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('redirects logger to logger dashboard after login', function () {
    $user = User::factory()->logger()->create();

    Livewire::test(Login::class)
        ->set('username', $user->username)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('logger_dashboard'));
});

it('redirects admin to admin dashboard after login', function () {
    $user = User::factory()->admin()->create();

    Livewire::test(Login::class)
        ->set('username', $user->username)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('admin_dashboard'));
});

it('redirects super admin to admin dashboard after login', function () {
    $user = User::factory()->superAdmin()->create();

    Livewire::test(Login::class)
        ->set('username', $user->username)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('admin_dashboard'));
});

it('shows error for non-existent username', function () {
    Livewire::test(Login::class)
        ->set('username', 'nonexistent')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('username');
});

it('shows error for wrong password', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('username', $user->username)
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors('password');
});

it('validates username is required', function () {
    Livewire::test(Login::class)
        ->set('username', '')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('username');
});

it('validates password is required', function () {
    Livewire::test(Login::class)
        ->set('username', 'testuser')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors('password');
});

it('redirects authenticated logger away from login page', function () {
    $user = User::factory()->logger()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('logger_dashboard'));
});

it('redirects authenticated admin away from login page', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('admin_dashboard'));
});
