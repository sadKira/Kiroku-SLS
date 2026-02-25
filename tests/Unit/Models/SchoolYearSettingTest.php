<?php

use App\Models\SchoolYearSetting;

it('has the correct fillable attributes', function () {
    $setting = new SchoolYearSetting();

    expect($setting->getFillable())->toEqual([
        'school_year',
        'is_active',
    ]);
});

it('casts is_active to boolean', function () {
    $setting = new SchoolYearSetting();
    $casts = $setting->getCasts();

    expect($casts['is_active'])->toBe('boolean');
});

it('getActive returns the active school year', function () {
    SchoolYearSetting::create(['school_year' => '2024-2025', 'is_active' => false]);
    SchoolYearSetting::create(['school_year' => '2025-2026', 'is_active' => true]);

    $active = SchoolYearSetting::getActive();

    expect($active)->toBeInstanceOf(SchoolYearSetting::class)
        ->and($active->school_year)->toBe('2025-2026')
        ->and($active->is_active)->toBeTrue();
});

it('getActive returns null when no active school year exists', function () {
    SchoolYearSetting::create(['school_year' => '2024-2025', 'is_active' => false]);

    $active = SchoolYearSetting::getActive();

    expect($active)->toBeNull();
});

it('setActive activates the specified school year and deactivates others', function () {
    SchoolYearSetting::create(['school_year' => '2024-2025', 'is_active' => true]);
    SchoolYearSetting::create(['school_year' => '2025-2026', 'is_active' => false]);

    SchoolYearSetting::setActive('2025-2026');

    expect(SchoolYearSetting::where('school_year', '2024-2025')->first()->is_active)->toBeFalse()
        ->and(SchoolYearSetting::where('school_year', '2025-2026')->first()->is_active)->toBeTrue();
});

it('setActive creates a new school year record if it does not exist', function () {
    SchoolYearSetting::setActive('2026-2027');

    $setting = SchoolYearSetting::where('school_year', '2026-2027')->first();

    expect($setting)->not->toBeNull()
        ->and($setting->is_active)->toBeTrue();
});
