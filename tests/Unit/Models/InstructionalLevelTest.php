<?php

use App\Models\InstructionalLevel;

it('has the correct fillable attributes', function () {
    $level = new InstructionalLevel();

    expect($level->getFillable())->toEqual([
        'code',
        'name',
    ]);
});

it('can create an instructional level', function () {
    $level = InstructionalLevel::create([
        'code' => 'COL',
        'name' => 'College',
    ]);

    expect($level)->toBeInstanceOf(InstructionalLevel::class)
        ->and($level->code)->toBe('COL')
        ->and($level->name)->toBe('College');
});

it('requires a unique code', function () {
    InstructionalLevel::create(['code' => 'COL', 'name' => 'College']);

    expect(fn () => InstructionalLevel::create(['code' => 'COL', 'name' => 'Duplicate']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
