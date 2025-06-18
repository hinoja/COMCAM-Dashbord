<?php

namespace App\Imports;

use App\Models\Titre;
use App\Models\Forme;
use App\Models\FormeEssence;
use App\Models\Type;
use App\Models\Essence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;

class TitreImport implements ToModel, SkipsEmptyRows, WithStartRow
{
    use Importable;

    private $successCount = 0;
    private $errorCount = 0;
    private $skippedCount = 0;

    public function chunkSize(): int
    {
        return 100;
    }

    public function startRow(): int
    {
        return 1;
    }

    private function normalizeText($text): string
    {
        if (!is_string($text)) {
            return '';
        }
        return strtr(mb_strtoupper(trim($text)), [
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'À' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'Î' => 'I', 'Ï' => 'I',
            'Ô' => 'O', 'Ö' => 'O',
            'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C'
        ]);
    }

    private function parseFormeAndType($formeText, $typeText = null): array
    {
        $formeText = $this->normalizeText($formeText);
        $typeText = $this->normalizeText($typeText ?? '');

        // Cas GRUME
        if (in_array($formeText, ['GRUME', '1', ''])) {
            return ['forme_id' => 1, 'type_id' => 1];
        }

        // Cas DÉBITÉ avec type spécifique
        if ($formeText === 'DEBITE' || $formeText === '2') {
            $typeMap = [
                '5N' => 2,
                '6.1' => 3,
                '6.2' => 4,
                'PS' => 5,
                '2' => 2,  // 5N
                '3' => 3,  // 6.1
                '4' => 4,  // 6.2
                '5' => 5   // PS
            ];

            $cleanType = trim(str_replace([' ', '.'], '', $typeText));

            if (isset($typeMap[$cleanType])) {
                return [
                    'forme_id' => 2,
                    'type_id' => $typeMap[$cleanType]
                ];
            }

            throw new \Exception("Type débité non reconnu: $typeText");
        }

        throw new \Exception("Forme non reconnue: $formeText");
    }

    private function formatVolume($volume): float
    {
        if (empty($volume)) {
            return 0.0;
        }

        $volume = str_replace([' ', ','], ['', '.'], trim($volume));
        return (float) number_format((float) $volume, 3, '.', '');
    }

    public function model(array $row)
    {
        // Vérification des colonnes obligatoires
        if (empty($row[0]) || empty($row[1]) || empty($row[5]) || empty($row[6])) {
            $this->skippedCount++;
            Log::info('Ligne ignorée - colonnes obligatoires manquantes', ['row' => $row]);
            return null;
        }

        try {
            DB::beginTransaction();

            // 1. Gestion du titre
            $titre = Titre::firstOrCreate(
                ['nom' => $this->normalizeText($row[1])],
                [
                    'exercice' => (int) $row[0],
                    'localisation' => $this->normalizeText($row[2] ?? ''),
                    'zone_id' => (int) ($row[3] ?? 1), // Valeur par défaut 1 si non spécifié
                ]
            );

            // 2. Vérification de l'essence
            $essenceId = (int) $row[5];
            if (!Essence::where('id', $essenceId)->exists()) {
                throw new \Exception("Essence introuvable avec l'ID: $essenceId");
            }

            // 3. Parse forme et type
            $formeData = $this->parseFormeAndType($row[6], $row[7] ?? null);

            // 4. Validation volume
            $volume = $this->formatVolume($row[8] ?? 0);
            if ($volume <= 0) {
                throw new \Exception("Volume invalide: {$row[8]}");
            }

            // 5. Mise à jour ou création dans la table pivot
            $titre->essence()->syncWithoutDetaching([
                $essenceId => [
                    'volume' => $volume,
                    'VolumeRestant' => $volume,
                    'forme_id' => $formeData['forme_id'],
                    'type_id' => $formeData['type_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            // 6. Mise à jour FormeEssence (optionnel)
            FormeEssence::updateOrCreate(
                [
                    'essence_id' => $essenceId,
                    'forme_id' => $formeData['forme_id'],
                    'type_id' => $formeData['type_id']
                ],
                [
                    'updated_at' => now()
                ]
            );

            DB::commit();
            $this->successCount++;

            Log::info('Import réussi', [
                'titre' => $titre->nom,
                'essence_id' => $essenceId,
                'volume' => $volume,
                'forme_id' => $formeData['forme_id'],
                'type_id' => $formeData['type_id']
            ]);

            return $titre;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;

            Log::error('Erreur lors de l\'import', [
                'row' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function getResultStats(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'skipped_count' => $this->skippedCount,
            'total' => $this->successCount + $this->errorCount + $this->skippedCount,
            'success_rate' => ($this->successCount + $this->errorCount > 0)
                ? round(($this->successCount / ($this->successCount + $this->errorCount)) * 100, 2)
                : 0
        ];
    }
}
