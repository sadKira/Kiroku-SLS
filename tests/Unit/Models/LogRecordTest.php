<?php

use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use App\Models\Faculty;

it('has the correct fillable attributes', function () {
    $logRecord = new LogRecord();

    expect($logRecord->getFillable())->toEqual([
        'loggable_type',
        'student_id',
        'faculty_id',
        'log_session_id',
        'time_in',
        'time_out',
    ]);
});

it('belongs to a log session', function () {
    $logSession = LogSession::factory()->create();
    $student = Student::factory()->create();

    $logRecord = LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'student_id' => $student->id,
        'loggable_type' => 'student',
    ]);

    expect($logRecord->logSessions)->toBeInstanceOf(LogSession::class)
        ->and($logRecord->logSessions->id)->toBe($logSession->id);
});

it('belongs to a student', function () {
    $logSession = LogSession::factory()->create();
    $student = Student::factory()->create();

    $logRecord = LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'student_id' => $student->id,
        'loggable_type' => 'student',
    ]);

    expect($logRecord->student)->toBeInstanceOf(Student::class)
        ->and($logRecord->student->id)->toBe($student->id);
});

it('belongs to a faculty', function () {
    $logSession = LogSession::factory()->create();
    $faculty = Faculty::factory()->create();

    $logRecord = LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'faculty_id' => $faculty->id,
        'student_id' => null,
        'loggable_type' => 'faculty',
    ]);

    expect($logRecord->faculty)->toBeInstanceOf(Faculty::class)
        ->and($logRecord->faculty->id)->toBe($faculty->id);
});

it('defaults loggable_type to student', function () {
    $logSession = LogSession::factory()->create();
    $student = Student::factory()->create();

    $logRecord = LogRecord::factory()->create([
        'log_session_id' => $logSession->id,
        'student_id' => $student->id,
    ]);

    expect($logRecord->loggable_type)->toBe('student');
});
