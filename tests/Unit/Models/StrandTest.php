<?php

use App\Models\Strand;

it('has the correct fillable attributes', function () {
    $strand = new Strand();

    expect($strand->getFillable())->toEqual([
        'code',
        'name',
    ]);
});

it('can create a strand', function () {
    $strand = Strand::create([
        'code' => 'STEM',
        'name' => 'Science, Technology, Engineering, and Mathematics',
    ]);

    expect($strand)->toBeInstanceOf(Strand::class)
        ->and($strand->code)->toBe('STEM')
        ->and($strand->name)->toBe('Science, Technology, Engineering, and Mathematics');
});

it('requires a unique code', function () {
    Strand::create(['code' => 'STEM', 'name' => 'Science, Technology, Engineering, and Mathematics']);

    expect(fn () => Strand::create(['code' => 'STEM', 'name' => 'Duplicate']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
