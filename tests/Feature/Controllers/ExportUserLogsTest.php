<?php

use App\Models\Faculty;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use App\Models\User;

it('redirects unauthenticated users away from the export student logs route', function () {
    $response = $this->get(route('export_user_logs', [
        'log_session_id' => 1,
        'paper_size' => 'A4',
    ]));

    $response->assertStatus(302);
});

it('redirects non-admin users away from the export student logs route', function () {
    $logger = User::factory()->create(['role' => 'logger']);

    $response = $this->actingAs($logger)->get(route('export_user_logs', [
        'log_session_id' => 1,
        'paper_size' => 'A4',
    ]));

    $response->assertStatus(302);
});

it('redirects back with error when log_session_id is missing', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('export_user_logs'));

    $response->assertRedirect();
    $response->assertSessionHas('notify');
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
    expect($notify['content'])->toContain('Log session ID is required');
});

it('redirects back with error when log session does not exist', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('export_user_logs', [
        'log_session_id' => 9999, // assumes it doesn't exist
    ]));

    $response->assertRedirect();
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
    expect($notify['content'])->toContain('Log session not found');
});

it('redirects back with error when log session has no records', function () {
    $admin = User::factory()->admin()->create();
    $session = LogSession::factory()->create();

    $response = $this->actingAs($admin)->get(route('export_user_logs', [
        'log_session_id' => $session->id,
    ]));

    $response->assertRedirect();
    $notify = session('notify');
    expect($notify['type'])->toBe('error');
    expect($notify['content'])->toContain('No log records found for this session');
});

it('handles student and faculty export without eager load exceptions', function () {
    $admin = User::factory()->admin()->create();
    $session = LogSession::factory()->create();

    $collegeStudent = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    $shsStudent = Student::factory()->create(['user_type' => 'shs', 'strand' => 'STEM', 'course' => null]);
    $faculty = Faculty::factory()->create();

    // Create log record for college student
    LogRecord::factory()->create([
        'loggable_type' => 'student',
        'student_id' => $collegeStudent->id,
        'faculty_id' => null,
        'log_session_id' => $session->id,
    ]);

    // Create log record for SHS student
    LogRecord::factory()->create([
        'loggable_type' => 'student',
        'student_id' => $shsStudent->id,
        'faculty_id' => null,
        'log_session_id' => $session->id,
    ]);

    // Create log record for faculty
    LogRecord::factory()->create([
        'loggable_type' => 'faculty',
        'student_id' => null,
        'faculty_id' => $faculty->id,
        'log_session_id' => $session->id,
    ]);

    $response = $this->actingAs($admin)->get(route('export_user_logs', [
        'log_session_id' => $session->id,
    ]));

    // We either get a successful browsershot conversion (200 PDF) or a browsershot system error (302)
    // The key is it bypasses the ModelNotFoundException or LazyLoadingViolationException
    if ($response->status() === 302) {
        $response->assertSessionHas('notify');
        $notify = session('notify');
        expect($notify['type'])->toBe('error');
        // It shouldn't crash with 500 lazy load violation
        expect($notify['content'])->toContain('Unable to generate PDF'); 
    } else {
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
});
