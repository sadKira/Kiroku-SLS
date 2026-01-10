<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use App\Models\LogRecord;
use App\Models\LogSession;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class ExportStudentLogs extends Controller
{
    public function generatePdf(Request $request)
    {
        try {
            // Get log session ID from request
            $logSessionId = $request->input('log_session_id');

            if (! $logSessionId) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'Log session ID is required.',
                    'duration' => 5000,
                ]);
            }

            // Get paper size from request, default to A4
            $paperSize = $request->input('paper_size', 'A4');

            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (! in_array($paperSize, $validPaperSizes)) {
                $paperSize = 'A4'; // Fallback to A4 if invalid
            }

            // Fetch log session with eager loaded log records and students
            $logSession = LogSession::with(['logRecords.student'])
                ->findOrFail($logSessionId);

            // Get log records ordered by time_in
            $logRecords = LogRecord::with('student')
                ->where('log_session_id', $logSessionId)
                ->orderBy('time_in')
                ->get();

            // Check if there are any log records to export
            if ($logRecords->isEmpty()) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'No log records found for this session. Please add log records first.',
                    'duration' => 5000,
                ]);
            }

            // Render the export view to HTML
            $html = view('Reports/export-student-logs', [
                'logSession' => $logSession,
                'logRecords' => $logRecords,
            ])->render();

            // Generate the PDF in memory and stream it to the browser
            $pdf = Browsershot::html($html)
                ->format($paperSize)    // use the selected paper size
                ->showBackground()      // ensure backgrounds are rendered
                ->margins(10, 10, 10, 10)
                ->scale(0.9)
                ->pdf();

            // Generate filename with date and academic year
            $dateFormatted = \Carbon\Carbon::parse($logSession->date)->format('Y-m-d');
            $filename = "student-logs-{$dateFormatted}-{$logSession->school_year}.pdf";

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);

        } catch (ModelNotFoundException $e) {
            // Handle model not found
            Log::error('Log session not found for export', [
                'log_session_id' => $request->input('log_session_id'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'Log session not found. Please refresh the page and try again.',
                'duration' => 5000,
            ]);

        } catch (\Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot $e) {
            // Handle Browsershot specific errors (e.g., Node.js not installed, Chrome/Chromium issues)
            Log::error('Browsershot error generating student logs PDF', [
                'error' => $e->getMessage(),
                'log_session_id' => $request->input('log_session_id'),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'Unable to generate PDF. Please ensure the PDF generation service is properly configured.',
                'duration' => 5000,
            ]);

        } catch (Exception $e) {
            // Handle any other unexpected errors
            Log::error('Error generating student logs PDF', [
                'error' => $e->getMessage(),
                'log_session_id' => $request->input('log_session_id'),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'An unexpected error occurred while generating the PDF. Please try again or contact support if the problem persists.',
                'duration' => 5000,
            ]);
        }
    }
}
