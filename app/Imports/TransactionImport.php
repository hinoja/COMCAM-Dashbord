<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\FormeEssence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;

class TransactionImport implements ToModel, SkipsEmptyRows
{
    use Importable;

    private $successCount = 0;
    private $errorCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Vérifier si la ligne n'est pas vide
        if (empty($row[0]) || !is_string($row[0])) {
            return null;
        }

        try {
            DB::beginTransaction();

            // Récupérer les données de la ligne
            $date = $row[0];                // Date de la transaction
            $exercice = $row[1];            // Année de l'exercice
            $numero = $row[2];              // Numéro de la transaction
            $societe_id = $row[3];          // ID de la société
            $destination = strtoupper($row[4]);  // Destination
            $pays = strtoupper($row[5]);         // Pays
            $titre_id = $row[6];            // ID du titre
            $essence_id = $row[7];          // ID de l'essence
            $forme_id = $row[8];            // ID de la forme
            $conditionnemment_id = $row[9]; // ID du conditionnement
            $type_id = $row[10];            // ID du type
            $volume = str_replace([' ', ','], ['', '.'], $row[11]);  // Volume

            // 1. Créer ou mettre à jour l'entrée dans FormeEssence
            $this->updateFormeEssence($essence_id, $forme_id, $type_id);

            // 2. Créer la transaction sans forme_id et type_id
            $transaction = Transaction::create([
                'date' => $date,
                'exercice' => $exercice,
                'numero' => $numero,
                'societe_id' => $societe_id,
                'destination' => $destination,
                'pays' => $pays,
                'titre_id' => $titre_id,
                'essence_id' => $essence_id,
                'conditionnemment_id' => $conditionnemment_id,
                'volume' => $volume,
            ]);

            // 3. Mettre à jour le volume restant dans la table pivot essence_titre
            $this->updateVolumeRestant($titre_id, $essence_id, $volume);

            DB::commit();
            $this->successCount++;

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;

            // Log l'erreur pour le débogage
            Log::error('Erreur lors de l\'importation d\'une transaction: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Crée ou met à jour l'entrée dans la table FormeEssence
     */
    private function updateFormeEssence(int $essenceId, int $formeId, int $typeId): void
    {
        // Vérifier si une entrée existe déjà
        $formeEssence = FormeEssence::where('essence_id', $essenceId)->first();

        if ($formeEssence) {
            // Mettre à jour l'entrée existante
            $formeEssence->update([
                'forme_id' => $formeId,
                'type_id' => $typeId
            ]);
        } else {
            // Créer une nouvelle entrée
            FormeEssence::create([
                'essence_id' => $essenceId,
                'forme_id' => $formeId,
                'type_id' => $typeId
            ]);
        }
    }

    /**
     * Met à jour le volume restant dans la table pivot essence_titre
     */
    private function updateVolumeRestant(int $titreId, int $essenceId, float $volume): void
    {
        // Récupérer l'entrée dans la table pivot
        $titre = \App\Models\Titre::find($titreId);

        if ($titre) {
            $pivotEntry = $titre->essence()
                ->where('essences.id', $essenceId)
                ->first();

            if ($pivotEntry) {
                $volumeRestant = $pivotEntry->pivot->VolumeRestant ?? $pivotEntry->pivot->volume;
                $newVolumeRestant = $volumeRestant - $volume;

                // Mettre à jour le volume restant
                $titre->essence()->updateExistingPivot($essenceId, [
                    'VolumeRestant' => $newVolumeRestant
                ]);
            }
        }
    }

    /**
     * Retourne les statistiques d'importation
     */
    public function getResultStats(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount
        ];
    }
}
