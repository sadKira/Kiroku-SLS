<?php

use App\Models\Faculty;
use App\Models\LogRecord;
use App\Models\LogSession;

it('has the correct fillable attributes', function () {
    $faculty = new Faculty();

    expect($faculty->getFillable())->toEqual([
        'id_faculty',
        'last_name',
        'first_name',
        'instructional_level',
    ]);
});

it('auto-generates a random faculty id starting with 1 (7 digits)', function () {
    $faculty = Faculty::factory()->create(['id_faculty' => null]);

    expect($faculty->id_faculty)
        ->toStartWith('1')
        ->toMatch('/^[0-9]{7}$/');
});

it('does not generate duplicate faculty ids', function () {
    $faculties = Faculty::factory()->count(10)->create(['id_faculty' => null]);
    $ids = $faculties->pluck('id_faculty');

    expect($ids->unique()->count())->toBe(10);
});

it('does not overwrite manually set faculty id', function () {
    $faculty = Faculty::factory()->create(['id_faculty' => '1888888']);

    expect($faculty->id_faculty)->toBe('1888888');
});

it('searches by last name', function () {
    Faculty::factory()->create(['last_name' => 'Garcia']);
    Faculty::factory()->create(['last_name' => 'Santos']);

    $results = Faculty::search('Garcia')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->last_name)->toBe('Garcia');
});

it('searches by first name', function () {
    Faculty::factory()->create(['first_name' => 'Maria']);
    Faculty::factory()->create(['first_name' => 'Juan']);

    $results = Faculty::search('Maria')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->first_name)->toBe('Maria');
});

it('searches by faculty id', function () {
    Faculty::factory()->create(['id_faculty' => '1123456']);
    Faculty::factory()->create(['id_faculty' => '1654321']);

    $results = Faculty::search('1123456')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id_faculty)->toBe('1123456');
});

it('searches by instructional level', function () {
    Faculty::factory()->create(['instructional_level' => 'College']);
    Faculty::factory()->create(['instructional_level' => 'Senior High School']);

    $results = Faculty::search('College')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->instructional_level)->toBe('College');
});

it('returns all faculty when search is empty', function () {
    Faculty::factory()->count(3)->create();

    $results = Faculty::search('')->get();

    expect($results)->toHaveCount(3);
});

it('has many log records', function () {
    $faculty = Faculty::factory()->create();
    $logSession = LogSession::factory()->create();

    LogRecord::factory()->create([
        'faculty_id' => $faculty->id,
        'student_id' => null,
        'log_session_id' => $logSession->id,
        'loggable_type' => 'faculty',
    ]);

    expect($faculty->logRecords)->toHaveCount(1);
});
