<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
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
            $userType = $request->input('user_type', 'college');
            
            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $paperSize = 'A4'; // Fallback to A4 if invalid
            }

            // Get users based on type
            if ($userType === 'faculty') {
                $users = Faculty::orderBy('id_faculty')->get();
                $idField = 'id_faculty';
                $label = 'faculty';
                $heading = 'Faculty Barcodes';
            } elseif ($userType === 'shs') {
                $users = Student::shs()->orderBy('id_student')->get();
                $idField = 'id_student';
                $label = 'shs-students';
                $heading = 'SHS Student Barcodes';
            } else {
                $users = Student::college()->orderBy('id_student')->get();
                $idField = 'id_student';
                $label = 'college-students';
                $heading = 'College Student Barcodes';
            }

            // Check if there are any users to export
            if ($users->isEmpty()) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'No users found to export. Please add users first.',
                    'duration' => 5000
                ]);
            }

            // Render the minimal barcode export view to HTML
            $html = view('Reports/export-barcode', [
                'students' => $users,
                'idField' => $idField,
                'heading' => $heading,
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
                'Content-Disposition' => 'attachment; filename="' . $label . '-barcodes-' . strtolower($paperSize) . '.pdf"',
            ]);

        } catch (\Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot $e) {
            Log::error('Browsershot error generating PDF', [
                'error' => $e->getMessage(),
                'paper_size' => $paperSize ?? 'unknown',
                'user_type' => $userType ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'Unable to generate PDF. Please ensure the PDF generation service is properly configured.',
                'duration' => 5000
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating barcode PDF', [
                'error' => $e->getMessage(),
                'paper_size' => $paperSize ?? 'unknown',
                'user_type' => $userType ?? 'unknown',
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
