<?php

use App\Models\Setting;

it('has the correct fillable attributes', function () {
    $setting = new Setting();

    expect($setting->getFillable())->toEqual([
        'key',
        'value',
    ]);
});

it('getAdminKey returns a query filtering by s_a_k key', function () {
    Setting::create(['key' => 's_a_k', 'value' => 'secret123']);
    Setting::create(['key' => 'other_key', 'value' => 'other_value']);

    $result = Setting::getAdminKey()->first();

    expect($result)->toBeInstanceOf(Setting::class)
        ->and($result->key)->toBe('s_a_k')
        ->and($result->value)->toBe('secret123');
});

it('getAdminKey returns null when no admin key exists', function () {
    Setting::create(['key' => 'other_key', 'value' => 'other_value']);

    $result = Setting::getAdminKey()->first();

    expect($result)->toBeNull();
});
