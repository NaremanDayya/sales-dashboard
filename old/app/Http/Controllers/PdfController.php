<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\SalesRep;
use App\Services\SalesRepPdfGenerator;
use App\Models\Client;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PdfController extends Controller
{
    public function exportSalesRepPdf($id)
    {
        $salesRep = SalesRep::with(['user', 'clients', 'agreements'])->findOrFail($id);

        $pdf = PDF::loadView('pdf.sales-rep-profile', compact('salesRep'), [], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
        return $pdf->download('تقرير_المندوب_' . $salesRep->name . '.pdf');
    }

    // Preview PDF inline for printing
    public function previewSalesRepPdf($id)
    {
        $salesRep = SalesRep::with(['user', 'clients', 'agreements', 'clientRequest'])->findOrFail($id);

        $pdf = PDF::loadView('preview.salesRep', compact('salesRep'),[], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);

        // Stream PDF to browser inline
        return $pdf->stream('تقرير_المندوب_' . $salesRep->name . '.pdf');
    }

    public function exportAgreementPdf(Request $request, $agreementId)
    {
        $agreement = Agreement::with(['client', 'salesRep', 'service'])->findOrFail($agreementId);

        $pdf = Pdf::loadView('pdf.agreement-profile', compact('agreement'),[], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
        $fileName = 'agreement_' . $agreement->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function previewAgreementPdf(Request $request, $agreementId)
    {
        $agreement = Agreement::with(['client', 'salesRep', 'service'])->findOrFail($agreementId);

        $pdf = Pdf::loadView('preview.agreement', compact('agreement'),[], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
        return $pdf->stream('preview_agreement_' . $agreement->id . '.pdf');
    }

    public function exportClientPdf(Request $request, $clientId)
    {
        $client = Client::with(['salesRep'])->findOrFail($clientId);

        $pdf = Pdf::loadView('pdf.client-profile', compact('client'),[], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);

        $fileName = 'client_' . $client->id . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function previewClientPdf(Request $request, $clientId)
    {
        $client = Client::with(['salesRep'])->findOrFail($clientId);

        $pdf = Pdf::loadView('preview.client-profile', compact('client'),[], [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);

        return $pdf->stream('preview_client_' . $client->id . '.pdf');
    }
}
