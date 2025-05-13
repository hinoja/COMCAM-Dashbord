<?php

namespace App\Exports;

use App\Models\Titre;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TitreExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithProperties
{
    protected $titres;

    public function __construct($query = null)
    {
        $this->titres = $query ?? $this->getDefaultQuery();
    }

    protected function getDefaultQuery()
    {
        return Titre::with([
            'zone',
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
                    $query->with(['forme', 'type']);
                }]);
            }
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'J';
        $lastRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D6A4F']
                ]
            ],
            'A1:' . $lastColumn . '1' => [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ]
            ],
            'A2:' . $lastColumn . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ]
            ]
        ];
    }

    public function collection()
    {
        $result = collect();

        $titres = $this->titres instanceof \Illuminate\Database\Eloquent\Builder
            ? $this->titres->get()
            : $this->titres;

        foreach ($titres as $titre) {
            foreach ($titre->essence as $essence) {
                $result->push($this->formatTitreData($titre, $essence));
            }
        }

        return $result;
    }

    protected function formatTitreData($titre, $essence)
    {
        return (object)[
            'id' => $titre->id,
            'exercice' => $titre->exercice,
            'nom' => $titre->nom,
            'localisation' => $titre->localisation,
            'zone' => $titre->zone,
            'essence' => $essence,
            'volume' => $essence->pivot->volume,
            'volumeRestant' => $essence->pivot->VolumeRestant,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exercice',
            'Nom',
            'Localisation',
            'Zone',
            'Essence',
            'Forme',
            'Type',
            'Volume (m³)',
            'Volume Restant (m³)',
        ];
    }

    public function map($item): array
    {
        $forme = $item->essence->formeEssence->forme ?? null;
        $type = $item->essence->formeEssence->type ?? null;

        return [
            $item->id,
            $item->exercice,
            $item->nom,
            $item->localisation,
            $item->zone->name ?? 'N/A',
            $item->essence->nom_local ?? 'N/A',
            $forme ? $forme->designation : 'N/A',
            $type ? $type->code : 'N/A',
            $this->formatNumber($item->volume),
            $this->formatNumber($item->volumeRestant),
        ];
    }

    protected function formatNumber($value)
    {
        return number_format((float) $value, 2, ',', ' ');
    }

    public function properties(): array
    {
        return [
            'creator' => config('app.name'),
            'title' => 'Liste des Titres',
            'description' => 'Liste des titres forestiers avec leurs essences',
            'subject' => 'Titres',
            'keywords' => 'titres,foret,essence',
            'category' => 'Titres',
            'company' => config('app.name'),
            'created' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
