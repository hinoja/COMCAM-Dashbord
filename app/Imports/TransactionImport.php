<?php

namespace App\Imports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransactionImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Transaction([
            'date' => $row[0],                // Date de la transaction
            'exercice' => $row[1],            // Année de l'exercice
            'numero' => $row[2],              // Numéro de la transaction
            'societe_id' => $row[3],          // ID de la société
            'destination' => $row[4],         // Destination
            'pays' => $row[5],                // Pays
            'titre_id' => $row[6],            // ID du titre
            'essence_id' => $row[7],          // ID de l'essence
            'forme_id' => $row[8],            // ID de la forme
            'conditionnemment_id' => $row[9], // ID du conditionnement
            'type_id' => $row[10],            // ID du type
            'volume' => $row[11],             // Volume
        ]);
    }
}
