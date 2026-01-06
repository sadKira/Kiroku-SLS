<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Spatie\Browsershot\Browsershot;

class ExportBarcode extends Controller
{
    public function generatePdf()
    {
        // Get all students ordered by their student ID for a predictable layout
        $students = Student::orderBy('id_student')->get();

        // Render the minimal barcode export view to HTML
        $html = view('Reports/export-barcode', [
            'students' => $students,
        ])->render();

        // Generate the PDF in memory and stream it to the browser
        $pdf = Browsershot::html($html)
            ->format('A4')          // use a predefined paper size (see Spatie docs)
            ->showBackground()      // ensure backgrounds are rendered
            ->margins(10, 10, 10, 10)
            ->scale(0.9)
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="student-barcodes.pdf"',
        ]);
    }
}
