<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WorkHistoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['الموظف', 'البريد الإلكتروني', 'تاريخ البداية', 'تاريخ النهاية', 'المدة', 'الحالة'];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['email'],
            optional($row['start_date'])->format('Y-m-d'),
            $row['end_date'] ? $row['end_date']->format('Y-m-d') : 'حتى الآن',
            $row['period_label'],
            $row['is_active'] ? 'نشط' : 'منتهي',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);

                $sheet = $event->sheet->getDelegate();
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');

                $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')
                    ->applyFromArray([
                        'font' => ['bold' => true, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
            },
        ];
    }
}
