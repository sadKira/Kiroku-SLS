<?php

use App\Models\Course;

it('has the correct fillable attributes', function () {
    $course = new Course();

    expect($course->getFillable())->toEqual([
        'code',
        'name',
    ]);
});

it('can create a course', function () {
    $course = Course::create([
        'code' => 'BSIS',
        'name' => 'Bachelor of Science in Information Systems',
    ]);

    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->code)->toBe('BSIS')
        ->and($course->name)->toBe('Bachelor of Science in Information Systems');
});

it('requires a unique code', function () {
    Course::create(['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems']);

    expect(fn () => Course::create(['code' => 'BSIS', 'name' => 'Duplicate']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
