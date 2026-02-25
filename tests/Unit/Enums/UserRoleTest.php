<?php

use App\Enums\UserRole;

it('has exactly three cases', function () {
    expect(UserRole::cases())->toHaveCount(3);
});

it('has a Logger case with value "logger"', function () {
    expect(UserRole::Logger->value)->toBe('logger');
});

it('has an Admin case with value "admin"', function () {
    expect(UserRole::Admin->value)->toBe('admin');
});

it('has a SuperAdmin case with value "super_admin"', function () {
    expect(UserRole::SuperAdmin->value)->toBe('super_admin');
});

it('can be created from string values', function () {
    expect(UserRole::from('logger'))->toBe(UserRole::Logger);
    expect(UserRole::from('admin'))->toBe(UserRole::Admin);
    expect(UserRole::from('super_admin'))->toBe(UserRole::SuperAdmin);
});

it('throws exception for invalid value', function () {
    UserRole::from('invalid');
})->throws(ValueError::class);
