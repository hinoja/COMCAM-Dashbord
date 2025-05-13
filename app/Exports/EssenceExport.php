<?php

namespace App\Exports;

use App\Models\Essence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EssenceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Essence::with(['formeEssence.forme', 'formeEssence.type'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom Local',
            'Code', 
        ];
    }

    public function map($essence): array
    {
        return [
            $essence->id,
            $essence->nom_local,
            $essence->code,
        ];
    }
}
