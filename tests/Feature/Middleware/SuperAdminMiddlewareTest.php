<?php

use App\Models\User;

// it('allows super admin to access admin-protected routes', function () {
//     $user = User::factory()->superAdmin()->create();

//     $response = $this->actingAs($user)->get('/admin-dashboard');

//     $response->assertStatus(200);
// });

it('denies logger access to admin-protected routes', function () {
    $user = User::factory()->logger()->create();

    $response = $this->actingAs($user)
        ->from('/dashboard')
        ->get('/admin-dashboard');

    $response->assertRedirect('/dashboard');
});

it('denies admin access to logger-protected routes', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)
        ->from('/admin-dashboard')
        ->get('/dashboard');

    $response->assertRedirect('/admin-dashboard');
});

// it('each role can only access its own routes', function () {
//     $logger = User::factory()->logger()->create();
//     $admin = User::factory()->admin()->create();
//     $superAdmin = User::factory()->superAdmin()->create();

//     // Logger can access logger routes
//     $this->actingAs($logger)->get('/dashboard')->assertStatus(200);

//     // Admin can access admin routes
//     $this->actingAs($admin)->get('/admin-dashboard')->assertStatus(200);

//     // Super admin can access admin routes
//     $this->actingAs($superAdmin)->get('/admin-dashboard')->assertStatus(200);
// });
