<?php

namespace App\Imports;

use App\Models\Societe;
use Maatwebsite\Excel\Concerns\ToModel;

class SocieteImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {

        return new Societe([
            'acronym'     => $row[0],
        ]);
    }
}
