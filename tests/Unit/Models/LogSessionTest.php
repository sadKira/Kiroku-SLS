<?php

use App\Models\LogSession;
use App\Models\LogRecord;
use App\Models\Student;
use App\Models\Faculty;

it('has the correct fillable attributes', function () {
    $logSession = new LogSession();

    expect($logSession->getFillable())->toEqual([
        'date',
        'school_year',
    ]);
});

it('uses date as route key name', function () {
    $logSession = new LogSession();

    expect($logSession->getRouteKeyName())->toBe('date');
});

it('has many log records', function () {
    $logSession = LogSession::factory()->create();
    $student = Student::factory()->create();

    LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'student_id' => $student->id,
        'loggable_type' => 'student',
    ]);

    expect($logSession->logRecords)->toHaveCount(1)
        ->and($logSession->logRecords->first())->toBeInstanceOf(LogRecord::class);
});

it('belongs to many students through log records', function () {
    $logSession = LogSession::factory()->create();
    $student = Student::factory()->create();

    LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'student_id' => $student->id,
        'loggable_type' => 'student',
    ]);

    expect($logSession->students)->toHaveCount(1)
        ->and($logSession->students->first())->toBeInstanceOf(Student::class);
});

it('belongs to many faculties through log records', function () {
    $logSession = LogSession::factory()->create();
    $faculty = Faculty::factory()->create();

    LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'faculty_id' => $faculty->id,
        'student_id' => null,
        'loggable_type' => 'faculty',
    ]);

    expect($logSession->faculties)->toHaveCount(1)
        ->and($logSession->faculties->first())->toBeInstanceOf(Faculty::class);
});

it('searches by school year', function () {
    LogSession::factory()->create(['school_year' => '2025-2026']);
    LogSession::factory()->create(['school_year' => '2024-2025']);

    $results = LogSession::search('2025-2026')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->school_year)->toBe('2025-2026');
});

it('returns all sessions when search is empty', function () {
    LogSession::factory()->count(3)->create();

    $results = LogSession::search('')->get();

    expect($results)->toHaveCount(3);
});

it('searches by month name', function () {
    LogSession::factory()->create(['date' => '2026-01-15']);
    LogSession::factory()->create(['date' => '2026-06-20']);

    $results = LogSession::search('january')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->date)->toBe('2026-01-15');
});
