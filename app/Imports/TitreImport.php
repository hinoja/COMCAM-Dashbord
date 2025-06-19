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
        $cleanVolume = str_replace([' ', ','], ['', '.'], trim($volume));

        // Accepter explicitement 0
        if ($cleanVolume === '0' || $cleanVolume === 0) {
            return 0.000;
        }

        if (!is_numeric($cleanVolume)) {
            throw new \Exception("Volume invalide: format incorrect");
        }

        $floatVolume = (float)$cleanVolume;
        if ($floatVolume < 0) {
            throw new \Exception("Volume invalide: valeur négative");
        }

        return number_format($floatVolume, 3, '.', '');
    }

    private function validateRow(array $row): void
    {
        if (count($row) !== 8) {
            throw new \Exception("Format de ligne incorrect");
        }

        $requiredFields = [
            0 => ['field' => 'année', 'type' => 'numeric'],
            1 => ['field' => 'code_titre', 'type' => 'string'],
            2 => ['field' => 'localité', 'type' => 'string'],
            3 => ['field' => 'zone_id', 'type' => 'numeric'],
            4 => ['field' => 'essence_id', 'type' => 'numeric'],
            5 => ['field' => 'forme_id', 'type' => 'numeric'],
            6 => ['field' => 'type_id', 'type' => 'numeric'],
            7 => ['field' => 'volume', 'type' => 'numeric']
        ];

        foreach ($requiredFields as $index => $validation) {
            // Modification pour accepter 0 comme valeur valide
            if (!isset($row[$index]) || ($validation['field'] !== 'volume' && empty($row[$index]))) {
                throw new \Exception("Champ {$validation['field']} manquant");
            }

            if ($validation['type'] === 'numeric') {
                $value = str_replace(',', '.', $row[$index]);
                if (!is_numeric($value) && $value !== '0') {
                    throw new \Exception("Le champ {$validation['field']} doit être numérique");
                }
            }
        }
    }

    public function model(array $row)
    {
        try {
            // Validation basique
            if (empty($row[0]) || !is_numeric($row[0])) {
                throw new \Exception("Année invalide");
            }

            // Log::info('Données reçues:', [
            //     'exercice' => $row[0],
            //     'code_titre' => $row[1],
            //     'localite' => $row[2],
            //     'zone_id' => $row[3],
            //     'essence_id' => $row[4],
            //     'type_id' => $row[5],
            //     'forme_id' => $row[6],
            //     'volume' => $row[7] ?? null
            // ]);

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

            // Validate volume
            if (!isset($row[7]) || (!is_numeric(str_replace(',', '.', $row[7])) && $row[7] !== '0')) {
                throw new \Exception("Volume invalide: {$row[7]}");
            }

            $volume = $this->formatVolume($row[7]);

            // Update or create essence relation
            $titre->essence()->syncWithoutDetaching([
                $row[4] => [ // essence_id est maintenant à l'index 4
                    'volume' => $volume,
                    'VolumeRestant' => $volume,
                    'type_id' => $row[6], // type_id direct du fichier
                    'forme_id' => $row[5], // forme_id direct du fichier
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            // Update FormeEssence
            FormeEssence::updateOrCreate(
                [
                    'essence_id' => $row[4],

                ],
                [
                    'forme_id' => $row[5],
                    'type_id' => $row[6]
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
            'total' => $this->successCount + $this->errorCount,
            'success_rate' => ($this->successCount + $this->errorCount > 0)
                ? round(($this->successCount / ($this->successCount + $this->errorCount)) * 100, 2)
                : 0
        ];
    }
}
