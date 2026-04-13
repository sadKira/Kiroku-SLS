<?php

use App\Models\Student;
use App\Models\LogRecord;
use App\Models\LogSession;

it('has the correct fillable attributes', function () {
    $student = new Student();

    expect($student->getFillable())->toEqual([
        'user_type',
        'id_student',
        'last_name',
        'first_name',
        'year_level',
        'course',
        'strand',
    ]);
});

it('auto-generates a random student id starting with 9 (7 digits)', function () {
    $student = Student::factory()->create(['id_student' => null]);

    expect($student->id_student)
        ->toStartWith('9')
        ->toMatch('/^[0-9]{7}$/');
});

it('does not generate duplicate student ids', function () {
    $students = Student::factory()->count(10)->create(['id_student' => null]);
    $ids = $students->pluck('id_student');

    expect($ids->unique()->count())->toBe(10);
});

it('does not overwrite manually set student id', function () {
    $student = Student::factory()->create(['id_student' => '9999999']);

    expect($student->id_student)->toBe('9999999');
});

it('scopes college students', function () {
    Student::factory()->create(['user_type' => 'college']);
    Student::factory()->create(['user_type' => 'shs']);
    Student::factory()->create(['user_type' => 'college']);

    $results = Student::college()->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn ($s) => $s->user_type === 'college'))->toBeTrue();
});

it('scopes shs students', function () {
    Student::factory()->create(['user_type' => 'shs']);
    Student::factory()->create(['user_type' => 'college']);
    Student::factory()->create(['user_type' => 'shs']);

    $results = Student::shs()->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn ($s) => $s->user_type === 'shs'))->toBeTrue();
});

it('searches by last name', function () {
    Student::factory()->create(['last_name' => 'Garcia']);
    Student::factory()->create(['last_name' => 'Santos']);

    $results = Student::search('Garcia')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->last_name)->toBe('Garcia');
});

it('searches by first name', function () {
    Student::factory()->create(['first_name' => 'Maria']);
    Student::factory()->create(['first_name' => 'Juan']);

    $results = Student::search('Maria')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->first_name)->toBe('Maria');
});

it('searches by student id', function () {
    Student::factory()->create(['id_student' => '9123456']);
    Student::factory()->create(['id_student' => '9654321']);

    $results = Student::search('9123456')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id_student)->toBe('9123456');
});

it('searches by course abbreviation', function () {
    Student::factory()->create(['user_type' => 'college', 'course' => 'Bachelor of Science in Information Systems']);
    Student::factory()->create(['user_type' => 'college', 'course' => 'Bachelor of Human Services']);

    $results = Student::search('bsis')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->course)->toBe('Bachelor of Science in Information Systems');
});

it('searches by strand', function () {
    Student::factory()->create(['user_type' => 'shs', 'strand' => 'STEM']);
    Student::factory()->create(['user_type' => 'shs', 'strand' => 'HUMSS']);

    $results = Student::search('stem')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->strand)->toBe('STEM');
});

it('returns all students when search is empty', function () {
    Student::factory()->count(3)->create();

    $results = Student::search('')->get();

    expect($results)->toHaveCount(3);
});

it('searches by year level', function () {
    Student::factory()->create(['year_level' => '1st Year']);
    Student::factory()->create(['year_level' => '4th Year']);

    $results = Student::search('1st')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->year_level)->toBe('1st Year');
});

it('has many log records', function () {
    $student = Student::factory()->create();
    $logSession = LogSession::factory()->create();

    LogRecord::factory()->create([
        'student_id' => $student->id,
        'log_session_id' => $logSession->id,
        'loggable_type' => 'student',
    ]);

    expect($student->logRecords)->toHaveCount(1);
});
