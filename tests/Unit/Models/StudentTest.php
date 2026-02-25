<?php

use App\Models\Student;

it('has the correct fillable attributes', function () {
    $student = new Student();

    expect($student->getFillable())->toEqual([
        'id_student',
        'last_name',
        'first_name',
        'year_level',
        'course',
    ]);
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
    Student::factory()->create(['id_student' => '1234567']);
    Student::factory()->create(['id_student' => '7654321']);

    $results = Student::search('1234567')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id_student)->toBe('1234567');
});

it('searches by course abbreviation', function () {
    Student::factory()->create(['course' => 'Bachelor of Science in Information Systems']);
    Student::factory()->create(['course' => 'Bachelor of Human Services']);

    $results = Student::search('bsis')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->course)->toBe('Bachelor of Science in Information Systems');
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
