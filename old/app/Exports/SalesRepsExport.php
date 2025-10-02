<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesRepsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $columns;
    private $columnLabels;

    public function __construct(array $columns, array $columnLabels)
    {
        $this->columns = $columns;
        $this->columnLabels = $columnLabels;
    }

    public function collection()
    {
        $dbColumns = ['name', 'start_work_date', 'work_duration', 'late_customers'];
        return \App\Models\SalesRep::with(['user', 'clients', 'agreements', 'clientRequest'])->get($dbColumns);
    }

    public function headings(): array
    {
        return array_values($this->columnLabels);
    }

    public function map($salesRep): array
    {
        return collect($this->columns)->map(function ($col) use ($salesRep) {
            switch ($col) {
                case 'start_work_date':
                    return optional($salesRep->start_work_date)->format('Y-m-d');
                case 'work_duration':
                    return $salesRep->work_duration;
                case 'total_agreements':
                    return $salesRep->agreements->count();
                case 'email':
                    return $salesRep->user?->email;
                case 'pending_requests':
                    return $salesRep->clientRequest->where('status', 'pending')->count();
                case 'target_customers':
                case 'interested_customers':
                    return $salesRep->clients->where('interest_status', 'interested')->count();
                case 'late_customers':
                    return $salesRep->late_customers;
                case 'total_requests':
                    return $salesRep->clientRequest->count();
                default:
                    return $salesRep->{$col};
            }
        })->toArray();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set RTL direction for the entire sheet
                $event->sheet->getDelegate()->setRightToLeft(true);

                // Set Arabic-compatible font for all cells
                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

                // Right-align all cells
                $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Make header row bold and centered
                $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'name' => 'Arial'
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
            },
        ];
    }
}
