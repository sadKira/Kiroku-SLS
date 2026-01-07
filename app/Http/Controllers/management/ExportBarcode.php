<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Spatie\Browsershot\Browsershot;

class ExportBarcode extends Controller
{
    public function generatePdf(Request $request)
    {
        // Get paper size from request, default to A4
        $paperSize = $request->input('paper_size', 'A4');
        
        // Validate paper size
        $validPaperSizes = ['A4', 'Letter', 'Legal'];
        if (!in_array($paperSize, $validPaperSizes)) {
            $paperSize = 'A4'; // Fallback to A4 if invalid
        }

        // Get all students ordered by their student ID for a predictable layout
        $students = Student::orderBy('id_student')->get();

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
    }
}
