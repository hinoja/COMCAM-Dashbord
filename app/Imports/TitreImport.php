<?php

namespace App\Imports;

use App\Models\Titre;
use App\Models\FormeEssence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;

class TitreImport implements ToModel, SkipsEmptyRows
{
    use Importable;

    private $successCount = 0;
    private $errorCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 1; // Commencer à la première ligne si pas d'en-têtes
    }

    public function headingRow(): int
    {
        return 0; // Aucune ligne d'en-tête
    }

    public function model(array $row)
    {
        if (empty($row[0]) || !is_numeric($row[0])) {
            return null;
        }

        try {
            DB::beginTransaction();

            // Recherche plus précise des doublons
            $existingTitre = Titre::where([
                'exercice' => $row[0],
                'nom' => strtoupper($row[1]),
                'localisation' => strtoupper($row[2]),
                'zone_id' => $row[3],
            ])
            ->whereHas('essence', function($query) use ($row) {
                $query->where('essence_id', $row[5]);
            })
            ->first();

            if ($existingTitre) {
                // Log le doublon pour traçabilité
                Log::info('Doublon détecté:', [
                    'exercice' => $row[0],
                    'nom' => strtoupper($row[1]),
                    'localisation' => strtoupper($row[2]),
                    'zone_id' => $row[3],
                    'essence_id' => $row[5]
                ]);

                $this->errorCount++;
                DB::rollBack();
                return null;
            }

            // Créer le titre
            $titre = Titre::create([
                'exercice' => $row[0],
                'nom' => strtoupper($row[1]),
                'localisation' => strtoupper($row[2]),
                'zone_id' => $row[3],
            ]);

            // Récupérer et nettoyer les IDs
            $essenceId = $row[5];
            $formeId = $row[6];
            $typeId = $row[7];
            $volume = str_replace([' ', ','], ['', '.'], $row[8]);

            // Créer ou mettre à jour l'entrée dans FormeEssence
            $this->updateFormeEssence($essenceId, $formeId, $typeId);

            // Créer l'entrée dans la table pivot essence_titre
            $titre->essence()->attach($essenceId, [
                'volume' => $volume,
                'VolumeRestant' => $volume,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->successCount++;

            return $titre;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            Log::error('Erreur lors de l\'importation d\'un titre: ' . $e->getMessage());
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
