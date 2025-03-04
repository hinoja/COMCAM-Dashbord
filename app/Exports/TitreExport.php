<?php

namespace App\Exports;

use App\Models\Titre;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TitreExport implements FromCollection
{
    public function styles(Worksheet $sheet)
    {
        return [
            // Styler la première ligne (en-têtes)
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => '2d6a4f']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'a8d5ba']]],
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Titre::with(['zone', 'essence', 'forme', 'type'])->get();
    }
    /**
     * Définit les en-têtes des colonnes dans le fichier Excel
     */
    public function headings(): array
    {
        return [
            'Exercice',
            'Nom',
            'Localisation',
            'Zone',
            'Essence',
            'Forme',
            'Type',
            'Volume (m³)',
        ];
    }
    /**
     * Mappe les données de chaque titre pour l’export
     */
    public function map($titre): array
    {
        return [
            $titre->exercice,
            $titre->nom,
            $titre->localisation,
            $titre->zone->name ?? 'N/A',
            $titre->essence->nom_local ?? 'N/A',
            $titre->forme->designation ?? 'N/A',
            $titre->type->code ?? 'N/A',
            number_format((float) $titre->volume, 2, ',', ' '),
        ];
    }
}
