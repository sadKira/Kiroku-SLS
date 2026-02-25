<?php

use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;

it('has the correct fillable attributes', function () {
    $logRecord = new LogRecord();

    expect($logRecord->getFillable())->toEqual([
        'student_id',
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
    ]);

    expect($logRecord->student)->toBeInstanceOf(Student::class)
        ->and($logRecord->student->id)->toBe($student->id);
});
