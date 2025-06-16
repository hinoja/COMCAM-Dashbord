<?php

namespace App\Imports;

use App\Models\Titre;
use App\Models\Forme;
use App\Models\FormeEssence;
use App\Models\Type;
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
        return 100;
    }

    public function startRow(): int
    {
        return 1;
    }

    private function normalizeText($text): string
    {
        return strtr(strtoupper(trim($text)), ['É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E']);
    }

    private function parseFormeAndType($formeText, $typeText = null): array
    {
        $formeText = $this->normalizeText($formeText);
        $typeText = $this->normalizeText($typeText ?? '');

        // Cas GRUME
        if (in_array($formeText, ['GRUME', '1'])) {
            return ['forme_id' => 1, 'type_id' => 1];
        }

        // Cas DÉBITÉ avec type spécifique
        if ($formeText === 'DEBITE' || $formeText === '2') {
            $typeMap = [
                '5N' => 2,
                '6.1' => 3,
                '6.2' => 4,
                'PS' => 5
            ];
            $cleanType = trim(str_replace(' ', '', $typeText));
            if (isset($typeMap[$cleanType])) {
                return [
                    'forme_id' => 2,
                    'type_id' => $typeMap[$cleanType]
                ];
            } else {
                // Type non reconnu pour débité : lever une erreur explicite
                Log::error('Type débité non reconnu:', [
                    'formeText' => $formeText,
                    'typeText' => $typeText
                ]);
                throw new \Exception("Type débité non reconnu: $typeText");
            }
        }

        // Cas par défaut : lever une erreur
        Log::error('Forme ou type non reconnu:', [
            'formeText' => $formeText,
            'typeText' => $typeText
        ]);
        throw new \Exception("Forme ou type non reconnu: $formeText / $typeText");
    }

    private function formatVolume($volume): float
    {
        $volume = str_replace([' ', ','], ['', '.'], trim($volume));
        return number_format((float)$volume, 3, '.', '');
    }

    public function model(array $row)
    {
        if (empty($row[0]) || !is_numeric($row[0])) {
            return null;
        }
        Log::info('Données reçues:', [
            'exercice' => $row[0],
            'titre' => $row[1],
            'forme' => $row[6],
            'type' => $row[7] ?? null,
            'volume' => $row[8] ?? null
        ]);
        try {
            DB::beginTransaction();

            // Create or find titre
            $titre = Titre::firstOrCreate(
                ['nom' => strtoupper($row[1])],
                [
                    'exercice' => $row[0],
                    'localisation' => strtoupper($row[2]),
                    'zone_id' => $row[3],
                ]
            );

            // Parse forme and type
            $formeData = $this->parseFormeAndType($row[6], $row[7] ?? null);

            // Validate volume
            if (empty($row[8]) || !is_numeric(str_replace(',', '.', $row[8]))) {
                throw new \Exception("Volume invalide: {$row[8]}");
            }

            $volume = $this->formatVolume($row[8]);

            // Update or create essence relation
            $titre->essence()->syncWithoutDetaching([
                $row[5] => [
                    'volume' => $volume,
                    'VolumeRestant' => $volume,
                    'type_id' => $formeData['type_id'],
                    'forme_id' => $formeData['forme_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            // Update FormeEssence
            FormeEssence::updateOrCreate(
                ['essence_id' => $row[5]],
                [
                    'forme_id' => $formeData['forme_id'],
                    'type_id' => $formeData['type_id']
                ]
            );

            DB::commit();
            $this->successCount++;
            return $titre;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            Log::error('Import error:', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getResultStats(): array
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'total' => $this->successCount + $this->errorCount,
            'success_rate' => ($this->successCount + $this->errorCount > 0)
                ? round(($this->successCount / ($this->successCount + $this->errorCount)) * 100, 2)
                : 0
        ];
    }
}
