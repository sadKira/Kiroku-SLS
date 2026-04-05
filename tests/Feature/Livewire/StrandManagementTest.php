<?php

use App\Livewire\Management\StrandManagement;
use App\Models\Strand;
use App\Models\User;
use Livewire\Livewire;

it('renders the strand management page for admin', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('strand_management'))
        ->assertStatus(200);
});

it('can store a new strand with formatting', function () {
    $user = User::factory()->admin()->create();

    // Verify it isn't taking duplicates and formatting applies
    Livewire::actingAs($user)
        ->test(StrandManagement::class)
        ->set('code', 'bsis')
        ->set('name', 'bachelor of science in information systems')
        ->call('storeStrand')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseHas('strands', [
        'code' => 'BSIS',
        'name' => 'Bachelor Of Science In Information Systems',
    ]);
});

it('cannot store a duplicate strand', function () {
    $user = User::factory()->admin()->create();

    Strand::create([
        'code' => 'BSIT',
        'name' => 'Bachelor of Science in Information Technology',
    ]);

    Livewire::actingAs($user)
        ->test(StrandManagement::class)
        ->set('code', 'BSIT')
        ->set('name', 'Some New Name')
        ->call('storeStrand')
        ->assertHasErrors(['code' => 'unique']);
});

it('can delete a strand', function () {
    $user = User::factory()->admin()->create();

    $strand = Strand::create([
        'code' => 'BSED',
        'name' => 'Bachelor of Secondary Education',
    ]);

    Livewire::actingAs($user)
        ->test(StrandManagement::class)
        ->call('confirmDelete', $strand->id)
        ->call('deleteStrand')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseMissing('strands', [
        'id' => $strand->id,
    ]);
});

it('can perform a search', function () {
    $user = User::factory()->admin()->create();

    Strand::create(['code' => 'ZETA', 'name' => 'Strand Zeta']);
    Strand::create(['code' => 'OMG', 'name' => 'Strand Omega']);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Management\StrandListTable::class)
        ->set('search', 'Omega')
        ->assertSee('OMG')
        ->assertDontSee('ZETA');
});
