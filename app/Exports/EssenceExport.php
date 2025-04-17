<?php

namespace App\Exports;

use App\Models\Essence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;

class EssenceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithProperties
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Essence::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Code',
            'Nom Local',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->code,
            $row->nom_local, 
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D6A4F']
                ]
            ],
            'A1:D1' => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator'        => config('app.name'),
            'title'          => 'Liste des Essences',
            'description'    => 'Liste des essences forestières',
            'subject'        => 'Essences',
            'keywords'       => 'essences,foret',
            'category'       => 'Essences',
            'company'        => config('app.name'),
        ];
    }
}
