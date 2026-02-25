<?php

use App\Models\User;
use App\Enums\UserRole;

it('has the correct fillable attributes', function () {
    $user = new User();

    expect($user->getFillable())->toEqual([
        'username',
        'name',
        'password',
        'role',
        'student_id',
        'name',
        'year_level',
        'course',
    ]);
});

it('hides password and remember_token from serialization', function () {
    $user = new User();

    expect($user->getHidden())->toContain('password', 'remember_token');
});

it('casts role to UserRole enum', function () {
    $user = new User();
    $casts = $user->getCasts();

    expect($casts['role'])->toBe(UserRole::class);
});

it('casts password as hashed', function () {
    $user = new User();
    $casts = $user->getCasts();

    expect($casts['password'])->toBe('hashed');
});

it('returns correct initials for a multi-word name', function () {
    $user = new User(['name' => 'John Doe']);

    expect($user->initials())->toBe('JD');
});

it('returns correct initials for a single-word name', function () {
    $user = new User(['name' => 'Admin']);

    expect($user->initials())->toBe('A');
});

it('returns correct initials for a three-word name', function () {
    $user = new User(['name' => 'John Michael Doe']);

    expect($user->initials())->toBe('JM');
});
