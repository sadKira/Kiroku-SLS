<?php

use App\Livewire\Management\LevelManagement;
use App\Models\InstructionalLevel;
use App\Models\User;
use Livewire\Livewire;

it('renders the level management page for admin', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('level_management'))
        ->assertStatus(200);
});

it('can store a new level with formatting', function () {
    $user = User::factory()->admin()->create();

    Livewire::actingAs($user)
        ->test(LevelManagement::class)
        ->set('code', 'col')
        ->set('name', 'college')
        ->call('storeLevel')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseHas('instructional_levels', [
        'code' => 'COL',
        'name' => 'College',
    ]);
});

it('cannot store a duplicate level', function () {
    $user = User::factory()->admin()->create();

    InstructionalLevel::create([
        'code' => 'KND',
        'name' => 'Kindergarten',
    ]);

    Livewire::actingAs($user)
        ->test(LevelManagement::class)
        ->set('code', 'KND')
        ->set('name', 'Some New Name')
        ->call('storeLevel')
        ->assertHasErrors(['code' => 'unique']);
});

it('can delete a level', function () {
    $user = User::factory()->admin()->create();

    $level = InstructionalLevel::create([
        'code' => 'JHS',
        'name' => 'Junior High School',
    ]);

    Livewire::actingAs($user)
        ->test(LevelManagement::class)
        ->call('confirmDelete', $level->id)
        ->call('deleteLevel')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseMissing('instructional_levels', [
        'id' => $level->id,
    ]);
});

it('can perform a search', function () {
    $user = User::factory()->admin()->create();

    InstructionalLevel::create(['code' => 'ZETA', 'name' => 'Level Zeta']);
    InstructionalLevel::create(['code' => 'OMG', 'name' => 'Level Omega']);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Management\LevelListTable::class)
        ->set('search', 'Omega')
        ->assertSee('OMG')
        ->assertDontSee('ZETA');
});
