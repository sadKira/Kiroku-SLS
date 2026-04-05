<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use App\Models\LogSession;
use App\Models\LogRecord;
use App\Models\Student;
use App\Models\Faculty;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.view-logs-app')]
class ViewLogs extends Component
{
    public LogSession $logSession;
    
    // Barcode input
    public $barcode = '';
    
    // User data for display
    public $userName = '';
    public $userDetail = '';
    public $userSubDetail = '';
    public $userType = '';

    public function mount(LogSession $logSession)
    {
        $this->logSession = $logSession->load(['logRecords.student', 'logRecords.faculty']);
    }

    public function updatedBarcode()
    {
        // Auto-submit when barcode is scanned (7 digits)
        if (strlen($this->barcode) === 7) {
            $this->scanBarcode();
        }
    }

    public function scanBarcode()
    {
        // Validate barcode format
        $this->validate([
            'barcode' => ['required', 'string', 'size:7', 'regex:/^[0-9]{7}$/'],
        ], [
            'barcode.required' => 'Please scan a barcode.',
            'barcode.size' => 'Barcode must be exactly 7 digits.',
            'barcode.regex' => 'Barcode must contain only numbers.',
        ]);

        try {
            // Try to find the user - first check students, then faculty
            $student = Student::where('id_student', $this->barcode)->first();
            $faculty = !$student ? Faculty::where('id_faculty', $this->barcode)->first() : null;

            if (!$student && !$faculty) {
                $this->dispatch('scan-error', message: 'User not found. Please check the barcode and try again.');
                $this->resetBarcode();
                return;
            }

            $now = Carbon::now('Asia/Manila');

            if ($student) {
                $this->processStudentScan($student, $now);
            } else {
                $this->processFacultyScan($faculty, $now);
            }

            // Refresh the log records table
            $this->dispatch('refresh-logs-table');

        } catch (ValidationException $e) {
            $this->dispatch('scan-error', message: $e->getMessage());
        } catch (\Exception $e) {
            $this->dispatch('scan-error', message: 'An error occurred. Please try again.');
        }

        // Reset barcode after processing
        $this->resetBarcode();
    }

    private function processStudentScan(Student $student, Carbon $now)
    {
        // Find the most recent log record for this student in this session
        $logRecord = LogRecord::where('log_session_id', $this->logSession->id)
            ->where('student_id', $student->id)
            ->where('loggable_type', 'student')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$logRecord) {
            // No previous record - create new
            LogRecord::create([
                'loggable_type' => 'student',
                'student_id' => $student->id,
                'log_session_id' => $this->logSession->id,
                'time_in' => $now,
                'time_out' => null,
            ]);
        } elseif ($logRecord->time_in && !$logRecord->time_out) {
            // Has time_in but no time_out - set time_out
            $logRecord->update(['time_out' => $now]);
        } else {
            // Has both - create a new record
            LogRecord::create([
                'loggable_type' => 'student',
                'student_id' => $student->id,
                'log_session_id' => $this->logSession->id,
                'time_in' => $now,
                'time_out' => null,
            ]);
        }

        // Set user data for display
        $this->userName = $student->last_name . ', ' . $student->first_name;
        $this->userDetail = $student->year_level;
        $this->userSubDetail = $student->user_type === 'shs' ? $student->strand : $student->course;
        $this->userType = $student->user_type === 'shs' ? 'SHS' : 'College';

        $this->dispatch('scan-label');
        $this->dispatch('scan-success');
    }

    private function processFacultyScan(Faculty $faculty, Carbon $now)
    {
        // Find the most recent log record for this faculty in this session
        $logRecord = LogRecord::where('log_session_id', $this->logSession->id)
            ->where('faculty_id', $faculty->id)
            ->where('loggable_type', 'faculty')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$logRecord) {
            LogRecord::create([
                'loggable_type' => 'faculty',
                'faculty_id' => $faculty->id,
                'log_session_id' => $this->logSession->id,
                'time_in' => $now,
                'time_out' => null,
            ]);
        } elseif ($logRecord->time_in && !$logRecord->time_out) {
            $logRecord->update(['time_out' => $now]);
        } else {
            LogRecord::create([
                'loggable_type' => 'faculty',
                'faculty_id' => $faculty->id,
                'log_session_id' => $this->logSession->id,
                'time_in' => $now,
                'time_out' => null,
            ]);
        }

        // Set user data for display
        $this->userName = $faculty->last_name . ', ' . $faculty->first_name;
        $this->userDetail = $faculty->instructional_level;
        $this->userSubDetail = '';
        $this->userType = 'Faculty';

        $this->dispatch('scan-label');
        $this->dispatch('scan-success');
    }

    private function resetBarcode()
    {
        $this->barcode = '';
    }

    public function render()
    {
        return view('livewire.logger.view-logs');
    }
}