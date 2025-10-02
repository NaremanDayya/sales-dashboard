<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class SalesRepPdfGenerator
{
    public function generate($salesRep, $preview = false)
    {
        $html = view('pdf.sales-rep-profile', ['salesRep' => $salesRep])->render();

        $filename = 'sales-reps/profile-' . $salesRep->id . '-' . now()->format('YmdHis') . '.pdf';

        $pdf = Browsershot::html($html)
            ->setNodeBinary(config('services.browsershot.node_path'))
            ->setNpmBinary(config('services.browsershot.npm_path'))
            ->margins(10, 10, 10, 10)
            ->format('A4')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->setOption('args', ['--disable-web-security']) // For font loading
            ->emulateMedia('screen');

        if ($preview) {
            return $pdf->pdf();
        }

        $pdf->save(storage_path('app/public/' . $filename));

        return $filename;
    }

    public function getPdfUrl($filename)
    {
        return Storage::disk('public')
        ->url($filename);
    }
}
