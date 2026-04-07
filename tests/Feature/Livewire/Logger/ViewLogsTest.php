<?php

use App\Livewire\Logger\ViewLogs;
use App\Models\Faculty;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use Livewire\Livewire;

it('renders the view logs component successfully', function () {
    $session = LogSession::factory()->create();

    Livewire::test(ViewLogs::class, ['logSession' => $session])
        ->assertStatus(200)
        ->assertViewIs('livewire.logger.view-logs');
});

it('handles initial scan as log in for student', function () {
    $session = LogSession::factory()->create();
    $student = Student::factory()->create(['id_student' => '1234567']);

    Livewire::test(ViewLogs::class, ['logSession' => $session])
        ->set('barcode', '1234567')
        ->assertDispatched('scan-success');

    $this->assertDatabaseHas('log_records', [
        'log_session_id' => $session->id,
        'loggable_type' => 'student',
        'student_id' => $student->id,
        'time_out' => null,
    ]);
});

it('handles duplicate logs correctly for user logging in and out multiple times', function () {
    $session = LogSession::factory()->create();
    $student = Student::factory()->create(['id_student' => '7654321']);

    $component = Livewire::test(ViewLogs::class, ['logSession' => $session]);

    // First scan - Logs IN
    $this->travelTo(now());
    $component->set('barcode', '7654321');
    
    $recordsBeforePunchOut = LogRecord::where('student_id', $student->id)->get();
    expect($recordsBeforePunchOut)->toHaveCount(1);
    expect($recordsBeforePunchOut->first()->time_in)->not->toBeNull();
    expect($recordsBeforePunchOut->first()->time_out)->toBeNull();

    // Second scan - Logs OUT
    $this->travel(1)->second();
    $component->set('barcode', '7654321');

    $recordsAfterPunchOut = LogRecord::where('student_id', $student->id)->get();
    expect($recordsAfterPunchOut)->toHaveCount(1);
    expect($recordsAfterPunchOut->first()->time_in)->not->toBeNull();
    expect($recordsAfterPunchOut->first()->time_out)->not->toBeNull();

    // Third scan - Logs IN again (creates a new row!)
    $this->travel(1)->second();
    $component->set('barcode', '7654321');

    $recordsAfterSecondIn = LogRecord::where('student_id', $student->id)->get();
    expect($recordsAfterSecondIn)->toHaveCount(2);

    $latestLog = $recordsAfterSecondIn->sortByDesc('created_at')->first();
    expect($latestLog->time_in)->not->toBeNull();
    expect($latestLog->time_out)->toBeNull();

    // Fourth scan - Logs OUT again
    $this->travel(1)->second();
    $component->set('barcode', '7654321');

    $recordsAfterSecondOut = LogRecord::where('student_id', $student->id)->get();
    expect($recordsAfterSecondOut)->toHaveCount(2);

    $latestLogOut = $recordsAfterSecondOut->sortByDesc('created_at')->first();
    expect($latestLogOut->time_in)->not->toBeNull();
    expect($latestLogOut->time_out)->not->toBeNull();
});

it('handles duplicate logs correctly for faculty logging in and out multiple times', function () {
    $session = LogSession::factory()->create();
    $faculty = Faculty::factory()->create(['id_faculty' => '9999999']);

    $component = Livewire::test(ViewLogs::class, ['logSession' => $session]);

    // 1st scan - IN
    $this->travelTo(now());
    $component->set('barcode', '9999999');
    $log1 = LogRecord::where('faculty_id', $faculty->id)->first();
    expect($log1->time_out)->toBeNull();

    // 2nd scan - OUT
    $this->travel(1)->second();
    $component->set('barcode', '9999999');
    $log1->refresh();
    expect($log1->time_out)->not->toBeNull();

    // 3rd scan - NEW IN
    $this->travel(1)->second();
    $component->set('barcode', '9999999');
    expect(LogRecord::where('faculty_id', $faculty->id)->count())->toBe(2);
});

it('shows an error for an invalid barcode', function () {
    $session = LogSession::factory()->create();

    Livewire::test(ViewLogs::class, ['logSession' => $session])
        ->set('barcode', '0000000') // not existing in db
        ->assertDispatched('scan-error');
});
