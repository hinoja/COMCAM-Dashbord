<?php

namespace App\Imports;

use App\Models\Essence;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Essence([
            'nom_local'     => $row[0],
           'code'    => $row[1],
        ]);
    }
}
