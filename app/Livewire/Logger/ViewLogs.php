<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use App\Models\LogSession;
use App\Models\LogRecord;
use App\Models\Student;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.view-logs-app')]
class ViewLogs extends Component
{
    public LogSession $logSession;
    
    // Barcode input
    public $barcode = '';
    
    // Student data for display
    public $studentName = '';
    public $studentYearLevel = '';
    public $studentCourse = '';

    public function mount(LogSession $logSession)
    {
        $this->logSession = $logSession->load('logRecords.student');
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
            // Find student by id_student
            $student = Student::where('id_student', $this->barcode)->first();

            if (!$student) {
                $this->dispatch('scan-error', message: 'Student not found. Please check the barcode and try again.');
                $this->resetBarcode();
                return;
            }

            // Find the most recent log record for this student in this session
            $logRecord = LogRecord::where('log_session_id', $this->logSession->id)
                ->where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $now = Carbon::now('Asia/Manila');

            if (!$logRecord) {
                // No previous record - create new log record with time_in
                LogRecord::create([
                    'student_id' => $student->id,
                    'log_session_id' => $this->logSession->id,
                    'time_in' => $now,
                    'time_out' => null,
                ]);

                // Set student data for display
                $this->studentName = $student->last_name . ', ' . $student->first_name;
                $this->studentYearLevel = $student->year_level;
                $this->studentCourse = $student->course;

                // Dispatch success events
                $this->dispatch('scan-label');
                $this->dispatch('scan-success');
                
            } elseif ($logRecord->time_in && !$logRecord->time_out) {
                // Most recent record has time_in but no time_out - set time_out
                $logRecord->update([
                    'time_out' => $now,
                ]);

                // Set student data for display
                $this->studentName = $student->last_name . ', ' . $student->first_name;
                $this->studentYearLevel = $student->year_level;
                $this->studentCourse = $student->course;

                // Dispatch success events
                $this->dispatch('scan-label');
                $this->dispatch('scan-success');
                
            } else {
                // Most recent record has both time_in and time_out - create a new log record
                LogRecord::create([
                    'student_id' => $student->id,
                    'log_session_id' => $this->logSession->id,
                    'time_in' => $now,
                    'time_out' => null,
                ]);

                // Set student data for display
                $this->studentName = $student->last_name . ', ' . $student->first_name;
                $this->studentYearLevel = $student->year_level;
                $this->studentCourse = $student->course;

                // Dispatch success events
                $this->dispatch('scan-label');
                $this->dispatch('scan-success');
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

    private function resetBarcode()
    {
        $this->barcode = '';
    }

    public function render()
    {
        return view('livewire.logger.view-logs');
    }
}