<?php

namespace App\Imports;

use App\Models\Titre;
use App\Models\Forme;
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
    private $formeCache = [];

    public function chunkSize(): int
    {
        return 100; // Traite 100 lignes à la fois
    }
    public function startRow(): int
    {
        return 1;
    }

    public function headingRow(): int
    {
        return 0;
    }

    private function updateFormeEssence(int $essenceId, $formeCode, int $typeId): void
    {
        try {
            // Analyser le code de forme
            $formeDesignation = $this->parseFormeCode($formeCode);

            // Rechercher ou créer la forme
            if (!isset($this->formeCache[$formeDesignation])) {
                $this->formeCache[$formeDesignation] = Forme::firstOrCreate(
                    ['designation' => $formeDesignation]
                );
            }
            $forme = $this->formeCache[$formeDesignation];

            // Vérifier si une entrée existe déjà
            $formeEssence = FormeEssence::where('essence_id', $essenceId)->first();

            if ($formeEssence) {
                // Mettre à jour l'entrée existante
                $formeEssence->update([
                    'forme_id' => $forme->id,
                    'type_id' => $typeId
                ]);
            } else {
                // Créer une nouvelle entrée
                FormeEssence::create([
                    'essence_id' => $essenceId,
                    'forme_id' => $forme->id,
                    'type_id' => $typeId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de FormeEssence:', [
                'message' => $e->getMessage(),
                'essence_id' => $essenceId,
                'forme_code' => $formeCode,
                'type_id' => $typeId
            ]);
            throw $e;
        }
    }

    private function parseFormeCode($code): string
    {
        // Nettoyer et standardiser le code
        $code = trim(strtoupper($code));

        // Gérer les cas spéciaux
        switch ($code) {
            case '6.1':
                return 'GRUME';
            case '6.2':
                return 'DEBITE';
            case 'PS':
                return 'PRODUIT SPECIAL';
            default:
                return $code;
        }
    }

    public function model(array $row)
    {
        if (empty($row[0]) || !is_numeric($row[0])) {
            return null;
        }

        try {
            DB::beginTransaction();

            // Rechercher le titre existant par nom exact
            $titre = Titre::where('nom', strtoupper($row[1]))->first();

            // Si le titre n'existe pas, le créer
            if (!$titre) {
                $titre = Titre::create([
                    'exercice' => $row[0],
                    'nom' => strtoupper($row[1]),
                    'localisation' => strtoupper($row[2]),
                    'zone_id' => $row[3],
                ]);
                Log::info('Nouveau titre créé:', ['nom' => $titre->nom]);
            }

            // Récupérer et nettoyer les IDs
            $essenceId = $row[5];
            $formeCode = $row[6]; // Maintenant nous passons le code de forme
            $typeId = $row[7];
            $volume = str_replace([' ', ','], ['', '.'], $row[8]);

            // Vérifier si la relation titre-essence existe déjà
            $relationExistante = $titre->essence()
                ->where('essence_id', $essenceId)
                ->first();

            if ($relationExistante) {
                // Mettre à jour le volume si nécessaire
                if ($relationExistante->pivot->Volume != $volume) {
                    $titre->essence()->updateExistingPivot($essenceId, [
                        'volume' => $volume,
                        'VolumeRestant' => $volume,
                        'updated_at' => now(),
                    ]);
                    Log::info('Volume mis à jour pour le titre existant:', [
                        'titre' => $titre->nom,
                        'essence_id' => $essenceId,
                        'ancien_volume' => $relationExistante->pivot->Volume,
                        'nouveau_volume' => $volume
                    ]);
                }
            } else {
                // Créer une nouvelle relation titre-essence
                $titre->essence()->attach($essenceId, [
                    'volume' => $volume,
                    'VolumeRestant' => $volume,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('Nouvelle relation titre-essence créée:', [
                    'titre' => $titre->nom,
                    'essence_id' => $essenceId,
                    'volume' => $volume
                ]);
            }

            // Créer ou mettre à jour l'entrée dans FormeEssence avec le code de forme
            $this->updateFormeEssence($essenceId, $formeCode, $typeId);

            DB::commit();
            $this->successCount++;

            return $titre;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            Log::error('Erreur lors de l\'importation d\'un titre:', [
                'message' => $e->getMessage(),
                'données' => $row
            ]);
            return null;
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
