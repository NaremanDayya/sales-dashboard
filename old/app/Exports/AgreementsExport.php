<?php

namespace App\Exports;

use App\Models\Agreement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AgreementsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $selectedColumns;

    public function __construct(array $selectedColumns)
    {
        $this->selectedColumns = $selectedColumns;
    }

    public function collection()
    {
        // Eager load relationships for performance
        return Agreement::with(['client', 'salesRep', 'service'])->get();
    }

    public function map($agreement): array
    {
        $row = [];

        foreach ($this->selectedColumns as $key) {
            switch ($key) {
                case 'client_company_name':
                    $row[] = optional($agreement->client)->company_name ?? '';
                    break;
                case 'sales_rep_name':
                    $row[] = optional($agreement->salesRep)->name ?? '';
                    break;
                case 'service_name':
                    $row[] = optional($agreement->service)->name ?? '';
                    break;
                case 'signing_date':
                    $row[] = optional($agreement->signing_date)?->format('Y-m-d') ?? '';
                    break;
                case 'duration_years':
                    $row[] = $agreement->duration_years ?? '';
                    break;
                case 'end_date':
                    $row[] = optional($agreement->end_date)?->format('Y-m-d') ?? '';
                    break;
                case 'total_amount':
                    $row[] = $agreement->total_amount ?? '';
                    break;
                default:
                    $row[] = '';
            }
        }

        return $row;
    }

    public function headings(): array
    {
        $availableColumns = [
            'client_company_name' => 'Client Name',
            'sales_rep_name' => 'Sales Rep Name',
            'service_name' => 'Service Name',
            'signing_date' => 'Signing Date',
            'duration_years' => 'Duration (Years)',
            'end_date' => 'End Date',
            'total_amount' => 'Total Amount',
        ];

        return array_map(fn($key) => $availableColumns[$key] ?? '', $this->selectedColumns);
    }

    public function columnSizes(): array
    {
        // Auto size columns based on content length
        // Here, setting all columns to auto-size
        $sizes = [];
        foreach ($this->selectedColumns as $index => $col) {
            // Columns start at A, B, C ...
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sizes[$letter] = -1; // -1 means auto-size in maatwebsite/excel
        }
        return $sizes;
    }
}
