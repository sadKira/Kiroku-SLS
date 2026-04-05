<?php

use App\Livewire\Management\CourseManagement;
use App\Models\Course;
use App\Models\User;
use Livewire\Livewire;

it('renders the course management page for admin', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('course_management'))
        ->assertStatus(200);
});

it('can store a new course with formatting', function () {
    $user = User::factory()->admin()->create();

    // Verify it isn't taking duplicates and formatting applies
    Livewire::actingAs($user)
        ->test(CourseManagement::class)
        ->set('code', 'bsis')
        ->set('name', 'bachelor of science in information systems')
        ->call('storeCourse')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseHas('courses', [
        'code' => 'BSIS',
        'name' => 'Bachelor Of Science In Information Systems',
    ]);
});

it('cannot store a duplicate course', function () {
    $user = User::factory()->admin()->create();

    Course::create([
        'code' => 'BSIT',
        'name' => 'Bachelor of Science in Information Technology',
    ]);

    Livewire::actingAs($user)
        ->test(CourseManagement::class)
        ->set('code', 'BSIT')
        ->set('name', 'Some New Name')
        ->call('storeCourse')
        ->assertHasErrors(['code' => 'unique']);
});

it('can delete a course', function () {
    $user = User::factory()->admin()->create();

    $course = Course::create([
        'code' => 'BSED',
        'name' => 'Bachelor of Secondary Education',
    ]);

    Livewire::actingAs($user)
        ->test(CourseManagement::class)
        ->call('confirmDelete', $course->id)
        ->call('deleteCourse')
        ->assertHasNoErrors()
        ->assertDispatched('notify');

    $this->assertDatabaseMissing('courses', [
        'id' => $course->id,
    ]);
});

it('can perform a search', function () {
    $user = User::factory()->admin()->create();

    Course::create(['code' => 'ZETA', 'name' => 'Course Zeta']);
    Course::create(['code' => 'OMG', 'name' => 'Course Omega']);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Management\CourseListTable::class)
        ->set('search', 'Omega')
        ->assertSee('OMG')
        ->assertDontSee('ZETA');
});
