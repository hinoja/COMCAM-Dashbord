<?php

namespace App\Imports;

use App\Models\Titre;
use Maatwebsite\Excel\Concerns\ToModel;

class TitreImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Titre([
            'exercice'     => $row[0],
            'nom'    => $row[1],
            'localisation'    => $row[2],
            'zone_id'    => $row[3],
            'essence_id'    => $row[4],
            'forme_id'    => $row[5],
            'type_id'    => $row[6],
            'volume'    => $row[7],
        ]);
    }
}
