<?php

use App\Http\Controllers\management\ExportDashboardReport;
use App\Models\Faculty;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\SchoolYearSetting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

// ─── Helper: seed a session + records for a given date/school-year ───────────

function seedSession(string $date, string $schoolYear, array $studentIds = [], array $facultyIds = []): LogSession
{
    $session = LogSession::factory()->create([
        'date'        => $date,
        'school_year' => $schoolYear,
    ]);

    foreach ($studentIds as $studentId) {
        LogRecord::factory()->create([
            'loggable_type'  => 'student',
            'student_id'     => $studentId,
            'faculty_id'     => null,
            'log_session_id' => $session->id,
            'time_in'        => Carbon::parse($date)->setHour(9)->setMinute(0),
            'time_out'       => Carbon::parse($date)->setHour(11)->setMinute(0),
        ]);
    }

    foreach ($facultyIds as $facultyId) {
        LogRecord::factory()->create([
            'loggable_type'  => 'faculty',
            'student_id'     => null,
            'faculty_id'     => $facultyId,
            'log_session_id' => $session->id,
            'time_in'        => Carbon::parse($date)->setHour(10)->setMinute(0),
            'time_out'       => Carbon::parse($date)->setHour(12)->setMinute(0),
        ]);
    }

    return $session;
}

// ─── Route access ─────────────────────────────────────────────────────────────

it('redirects unauthenticated users away from the export route', function () {
    $response = $this->get(route('export_dashboard_report', [
        'report_type' => 'monthly',
        'school_year' => '2025-2026',
        'month'       => 'January',
        'paper_size'  => 'A4',
    ]));

    // Unauthenticated users should be redirected (302) regardless of the login route name
    $response->assertStatus(302);
});

// ─── calculateStatistics – User Logs accuracy ─────────────────────────────────

it('counts user logs including duplicates (not unique users)', function () {
    $schoolYear = '2025-2026';
    $date       = '2026-01-15';

    $college = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    $faculty = Faculty::factory()->create();

    // Student logs in twice (two records)
    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $college->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-01-15 09:00:00']);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $college->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-01-15 13:00:00']);
    // Faculty logs in once
    LogRecord::factory()->create(['loggable_type' => 'faculty', 'student_id' => null, 'faculty_id' => $faculty->id, 'log_session_id' => $session->id, 'time_in' => '2026-01-15 10:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();
    $dateRange   = ['start' => $date, 'end' => $date];

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, $dateRange, 'monthly', $schoolYear);

    // 3 rows total (2 student + 1 faculty), not 2 unique
    expect($stats['totalUserLogs'])->toBe(3);
});

it('does NOT count unique users as user logs', function () {
    $schoolYear = '2025-2026';
    $date       = '2026-02-10';

    $s1 = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    $s2 = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $s1->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-02-10 09:00:00']);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $s2->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-02-10 10:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['totalUserLogs'])->toBe(2);
    expect($stats['uniqueUsers'])->toBe(2); // both unique
});

// ─── calculateStatistics – Total Users ───────────────────────────────────────

it('counts total users as college + shs + faculty (all time)', function () {
    Student::factory()->count(3)->create(['user_type' => 'college', 'course' => 'BSIS']);
    Student::factory()->count(2)->create(['user_type' => 'shs', 'strand' => 'STEM']);
    Faculty::factory()->count(1)->create();

    $schoolYear = '2025-2026';
    $date       = '2026-01-20';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    $student = Student::where('user_type', 'college')->first();
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $student->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-01-20 09:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['totalUsers'])->toBe(6);          // 3+2+1
    expect($stats['totalCollege'])->toBe(3);
    expect($stats['totalShs'])->toBe(2);
    expect($stats['totalFaculty'])->toBe(1);
});

// ─── calculateStatistics – Library Active Rate ────────────────────────────────

it('calculates library active rate correctly', function () {
    // 4 registered users, 2 log in → 50%
    $c1 = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    $f1 = Faculty::factory()->create();
    Faculty::factory()->create();

    $schoolYear = '2025-2026';
    $date       = '2026-03-05';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $c1->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-03-05 09:00:00']);
    LogRecord::factory()->create(['loggable_type' => 'faculty', 'student_id' => null, 'faculty_id' => $f1->id, 'log_session_id' => $session->id, 'time_in' => '2026-03-05 10:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['libraryActiveRate'])->toBe(50.0);
    expect($stats['uniqueUsers'])->toBe(2);
});

it('exposes monthlyActiveRate equal to libraryActiveRate for monthly reports', function () {
    // 2 registered users, 1 logs in → 50%
    $student = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    Student::factory()->create(['user_type' => 'college', 'course' => 'BSIT']);

    $schoolYear = '2025-2026';
    $date       = '2026-03-10';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $student->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-03-10 09:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['monthlyActiveRate'])->toBe($stats['libraryActiveRate']);
    expect($stats['monthlyActiveRate'])->toBe(50.0);
});

it('returns 0.0 for monthlyActiveRate when no users are registered', function () {
    $schoolYear = '2025-2026';
    $date       = '2026-03-15';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['monthlyActiveRate'])->toBe(0);
    expect($stats['libraryActiveRate'])->toBe(0);
});

// ─── calculateStatistics – Distributions ─────────────────────────────────────

it('builds course distribution from actual log counts (duplicates included)', function () {
    $student = Student::factory()->create(['user_type' => 'college', 'course' => 'BSIS']);
    $schoolYear = '2025-2026';
    $date       = '2026-02-20';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    // Same student logs in twice → 2 in course distribution
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $student->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-02-20 09:00:00']);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $student->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-02-20 14:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['courseDistribution']['BSIS'])->toBe(2);
});

it('builds strand distribution for shs students', function () {
    $student = Student::factory()->create(['user_type' => 'shs', 'strand' => 'STEM', 'course' => null]);
    $schoolYear = '2025-2026';
    $date       = '2026-02-22';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'student', 'student_id' => $student->id, 'faculty_id' => null, 'log_session_id' => $session->id, 'time_in' => '2026-02-22 09:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['strandDistribution'])->toHaveKey('STEM');
    expect($stats['strandDistribution']['STEM'])->toBe(1);
    // College course distribution should not include SHS
    expect($stats['courseDistribution'])->not->toHaveKey('STEM');
});

it('builds faculty distribution by instructional level', function () {
    $faculty = Faculty::factory()->create(['instructional_level' => 'College']);
    $schoolYear = '2025-2026';
    $date       = '2026-02-25';

    $session = LogSession::factory()->create(['date' => $date, 'school_year' => $schoolYear]);
    LogRecord::factory()->create(['loggable_type' => 'faculty', 'student_id' => null, 'faculty_id' => $faculty->id, 'log_session_id' => $session->id, 'time_in' => '2026-02-25 10:00:00']);
    LogRecord::factory()->create(['loggable_type' => 'faculty', 'student_id' => null, 'faculty_id' => $faculty->id, 'log_session_id' => $session->id, 'time_in' => '2026-02-25 14:00:00']);

    $controller = new ExportDashboardReport();
    $reflector  = new ReflectionMethod($controller, 'calculateStatistics');
    $reflector->setAccessible(true);

    $logSessions = LogSession::where('school_year', $schoolYear)->get();
    $logRecords  = LogRecord::whereIn('log_session_id', $logSessions->pluck('id'))->with(['student', 'faculty'])->get();

    $stats = $reflector->invoke($controller, $logRecords, $logSessions, ['start' => $date, 'end' => $date], 'monthly', $schoolYear);

    expect($stats['facultyDistribution'])->toHaveKey('College');
    expect($stats['facultyDistribution']['College'])->toBe(2); // both log rows counted
});

// ─── generatePdf – HTTP validation layer ─────────────────────────────────────

it('redirects back when no log sessions exist for selected period', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('export_dashboard_report', [
            'report_type' => 'monthly',
            'school_year' => '9999-10000',
            'month'       => 'January',
            'paper_size'  => 'A4',
        ]))
        ->assertRedirect();
});
