<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Spatie\Browsershot\Browsershot;
use Exception;
use Illuminate\Support\Facades\Log;

class ExportBarcode extends Controller
{
    public function generatePdf(Request $request)
    {
        try {
            // Get paper size from request, default to A4
            $paperSize = $request->input('paper_size', 'A4');
            
            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $paperSize = 'A4'; // Fallback to A4 if invalid
            }

            // Get all students ordered by their student ID for a predictable layout
            $students = Student::orderBy('id_student')->get();

            // Check if there are any students to export
            if ($students->isEmpty()) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'No students found to export. Please add students first.',
                    'duration' => 5000
                ]);
            }

            // Render the minimal barcode export view to HTML
            $html = view('Reports/export-barcode', [
                'students' => $students,
            ])->render();

            // Generate the PDF in memory and stream it to the browser
            $pdf = Browsershot::html($html)
                ->format($paperSize)    // use the selected paper size
                ->showBackground()      // ensure backgrounds are rendered
                ->margins(10, 10, 10, 10)
                ->scale(0.9)
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="student-barcodes-' . strtolower($paperSize) . '.pdf"',
            ]);

        } catch (\Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot $e) {
            // Handle Browsershot specific errors (e.g., Node.js not installed, Chrome/Chromium issues)
            Log::error('Browsershot error generating PDF', [
                'error' => $e->getMessage(),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'Unable to generate PDF. Please ensure the PDF generation service is properly configured.',
                'duration' => 5000
            ]);

        } catch (\Exception $e) {
            // Handle any other unexpected errors
            Log::error('Error generating barcode PDF', [
                'error' => $e->getMessage(),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'An unexpected error occurred while generating the PDF. Please try again or contact support if the problem persists.',
                'duration' => 5000
            ]);
        }
    }
}
