<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithProperties
{
    protected $transactions;

    public function __construct($query = null)
    {
        $this->transactions = $query ?? $this->getDefaultQuery();
    }

    protected function getDefaultQuery()
    {
        return Transaction::query()->with([
            'titre',
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
                    $query->with(['forme', 'type']);
                }]);
            },
            'societe',
            'conditionnemment'
        ])->orderBy('date', 'desc');
    }

    public function collection()
    {
        return $this->transactions instanceof \Illuminate\Database\Eloquent\Builder
            ? $this->transactions->get()
            : $this->transactions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Exercice',
            'Numéro',
            'Société',
            'Destination',
            'Pays',
            'Titre',
            'Essence',
            'Forme',
            'Conditionnement',
            'Type',
            'Volume'
        ];
    }

    public function map($transaction): array
    {
        // Utiliser l'opérateur null safe (?->)
        return [
            $transaction->id,
            $transaction->date ? $transaction->date->format('Y-m-d') : 'N/A',
            $transaction->exercice ?? 'N/A',
            $transaction->numero ?? 'N/A',
            $transaction->societe?->acronym ?? 'N/A',
            $transaction->destination ?? 'N/A',
            $transaction->pays ?? 'N/A',
            $transaction->titre?->nom ?? 'N/A',
            $transaction->essence?->nom_local ?? 'N/A',
            // Vérifier chaque niveau de la relation imbriquée
            $transaction->essence && $transaction->essence->formeEssence && $transaction->essence->formeEssence->forme
                ? $transaction->essence->formeEssence->forme->designation
                : 'N/A',
            $transaction->conditionnemment?->code ?? 'N/A',
            // Vérifier chaque niveau de la relation imbriquée
            $transaction->essence && $transaction->essence->formeEssence && $transaction->essence->formeEssence->type
                ? $transaction->essence->formeEssence->type->code
                : 'N/A',
            $this->formatNumber($transaction->volume)
        ];
    }

    protected function formatNumber($value)
    {
        return number_format((float) $value, 2, ',', ' ');
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'M';
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

    public function properties(): array
    {
        return [
            'creator' => config('app.name'),
            'title' => 'Liste des Transactions',
            'description' => 'Liste des transactions forestières',
            'subject' => 'Transactions',
            'keywords' => 'transactions,foret,essence',
            'category' => 'Transactions',
            'company' => config('app.name'),
            'created' => now()->format('Y-m-d H:i:s'),
        ];
    }
}



