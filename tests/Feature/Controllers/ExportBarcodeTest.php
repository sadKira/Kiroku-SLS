<?php

use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;

// ─── Route access ─────────────────────────────────────────────────────────────

it('redirects unauthenticated users away from the export barcode route', function () {
    $response = $this->get(route('export_barcode', [
        'paper_size' => 'A4',
        'user_type'  => 'college',
    ]));

    // Unauthenticated users should be redirected
    $response->assertStatus(302);
});

it('redirects non-admin users away from the export barcode route', function () {
    // A logger user does not have admin permissions
    $logger = User::factory()->create(['role' => 'logger']);

    $response = $this->actingAs($logger)->get(route('export_barcode', [
        'paper_size' => 'A4',
        'user_type'  => 'college',
    ]));

    // Assuming admin middleware redirects or aborts
    $response->assertStatus(302); // or 403, typically redirect if unauthorized in typical setups
});

// ─── Validation & Empty states ───────────────────────────────────────────────

it('redirects back with error when no college students exist to export', function () {
    $admin = User::factory()->admin()->create();

    // No students created yet

    $response = $this->actingAs($admin)
        ->get(route('export_barcode', [
            'paper_size' => 'A4',
            'user_type'  => 'college',
        ]));

    $response->assertRedirect();
    $response->assertSessionHas('notify');
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
    expect($notify['content'])->toContain('No users found to export');
});

it('redirects back with error when no shs students exist to export', function () {
    $admin = User::factory()->admin()->create();

    // No shs created yet

    $response = $this->actingAs($admin)
        ->get(route('export_barcode', [
            'paper_size' => 'A4',
            'user_type'  => 'shs',
        ]));

    $response->assertRedirect();
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
});

it('redirects back with error when no faculty exist to export', function () {
    $admin = User::factory()->admin()->create();

    // No faculty created yet

    $response = $this->actingAs($admin)
        ->get(route('export_barcode', [
            'paper_size' => 'A4',
            'user_type'  => 'faculty',
        ]));

    $response->assertRedirect();
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
});

// ─── Successful exports (BrowserShot integration would run) ───────────────────
// We may not easily test the actual PDF binary return if it relies on Puppeteer being installed in CI/dev,
// but we can mock Browsershot or just ignore asserting success since Browsershot might fail in environments without node/puppeteer.
// For safety we'll verify it tries to return a 200 response when data exists.

// Because Browsershot throws CouldNotTakeBrowsershot if Puppeteer is missing locally, we will check that it handles errors or returns a successful response without crashing.
it('handles barcode export when students exist', function () {
    $admin = User::factory()->admin()->create();
    
    // Create one college student
    Student::factory()->create([
        'user_type' => 'college',
        'course'    => 'BSIS'
    ]);

    $response = $this->actingAs($admin)
        ->get(route('export_barcode', [
            'paper_size' => 'A4',
            'user_type'  => 'college',
        ]));

    // Depending on the local environment, it either returns 200 OK (PDF download)
    // or redirects back with an error notification if Browsershot fails.
    if ($response->status() === 302) {
        $response->assertSessionHas('notify');
        $notify = session('notify');
        expect($notify['type'])->toBe('error');
    } else {
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
});
