<?php

namespace App\Exports;

use App\Models\Societe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;

class SocieteExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithProperties
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Societe::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'acronyme',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->acronym,
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
            'A1:L1' => [
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
            'title'          => 'Liste des Sociétés',
            'description'    => 'Liste des sociétés enregistrées',
            'subject'        => 'Sociétés',
            'keywords'       => 'sociétés,entreprises',
            'category'       => 'Sociétés',
            'company'        => config('app.name'),
        ];
    }
}

