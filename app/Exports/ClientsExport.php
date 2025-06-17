<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $columns;
    protected $availableColumns = [
        'company_name' => 'Company Name',
        'contact_person' => 'Contact Person',
        'salesrep_email' => 'Sales Rep Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'interest_status' => 'Interest Status',
        'last_contact_date' => 'Last Contact Date',
        'whatsapp_link' => 'WhatsApp Link',
    ];

    public function __construct(array $columns)
    {
        // Only accept columns which are in the available columns list
        $this->columns = array_intersect_key($this->availableColumns, array_flip($columns));
    }

    public function collection()
    {
        // Select only the columns requested by user
        return Client::select(array_keys($this->columns))->get();
    }

    public function headings(): array
    {
        // Return the headers in the order of selected columns
        return array_values($this->columns);
    }

    public function map($client): array
    {
        $row = [];
        foreach (array_keys($this->columns) as $column) {
            $value = $client->$column;

            // Optional: Format dates nicely, example for last_contact_date
            if ($column === 'last_contact_date' && $value) {
                $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
            }

            $row[] = $value;
        }
        return $row;
    }
}
