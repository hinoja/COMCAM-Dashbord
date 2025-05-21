<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use App\Models\Forme;
use App\Models\Type;
use App\Models\Conditionnemment;
use App\Models\FormeEssence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;

class TransactionImport implements ToModel, SkipsEmptyRows, WithValidation
{
    use Importable;

    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    /**
     * Définit la ligne à partir de laquelle commencer l'importation
     */
    public function startRow(): int
    {
        return 2; // Commence à la ligne 2 (après l'en-tête)
    }

    /**
     * Définit la ligne d'en-tête
     */
    public function headingRow(): int
    {
        return 1; // La première ligne est l'en-tête
    }

    /**
     * Règles de validation pour les données importées
     */
    public function rules(): array
    {
        return [
            '0' => ['required', function ($attribute, $value, $fail) {
                try {
                    // Vérifier si la date est déjà au format YYYY-MM-DD
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $date = Carbon::createFromFormat('Y-m-d', $value);
                    } else if (is_numeric($value)) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                    } else {
                        $fail('Le format de la date est invalide');
                        return;
                    }

                    if ($date->format('Y-m-d') > now()->format('Y-m-d')) {
                        $fail('La date ne peut pas être dans le futur');
                    }
                } catch (\Exception $e) {
                    $fail('Format de date invalide');
                }
            }],
            '1' => 'required|integer|min:2000', // Exercice
            '2' => 'required|integer|min:1', // Numéro de transaction
            '3' => 'required|exists:societes,id', // ID Exportateur
            '4' => 'required|string|max:255', // Destination
            '5' => 'required|string|max:255', // Pays
            '6' => 'required|exists:titres,id', // ID Titre
            '7' => 'required|exists:essences,id', // ID Essence
            '8' => 'required|exists:formes,id', // ID Forme
            '9' => 'required|exists:conditionnemments,id', // ID Conditionnement
            '10' => 'required|exists:types,id', // ID Type
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation
     */
    public function customValidationMessages()
    {
        return [
            '0.required' => 'La date est obligatoire',
            '0.date_format' => 'Le format de la date doit être AAAA-MM-JJ',
            '0.before_or_equal' => 'La date ne peut pas être dans le futur',
            '1.required' => 'L\'exercice est obligatoire',
            '1.integer' => 'L\'exercice doit être un nombre entier',
            '2.required' => 'Le numéro de transaction est obligatoire',
            '3.required' => 'L\'ID de l\'exportateur est obligatoire',
            '3.exists' => 'L\'exportateur spécifié n\'existe pas',
            '4.required' => 'La destination est obligatoire',
            '5.required' => 'Le pays est obligatoire',
            '6.required' => 'L\'ID du titre est obligatoire',
            '6.exists' => 'Le titre spécifié n\'existe pas',
            '7.required' => 'L\'ID de l\'essence est obligatoire',
            '7.exists' => 'L\'essence spécifiée n\'existe pas',
            '8.required' => 'L\'ID de la forme est obligatoire',
            '8.exists' => 'La forme spécifiée n\'existe pas',
            '9.required' => 'L\'ID du conditionnement est obligatoire',
            '9.exists' => 'Le conditionnement spécifié n\'existe pas',
            '10.required' => 'L\'ID du type est obligatoire',
            '10.exists' => 'Le type spécifié n\'existe pas',
        ];
    }

    /**
     * Traitement d'une ligne du fichier Excel   a revoir
     */
    // public function model(array $row)
    // {
    //     $this->rowNumber++;

    //     // Ignorer les lignes vides
    //     if (empty($row[0])) {
    //         return null;
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Traitement de la date (peut être au format Excel ou au format texte)
    //         $date = $this->parseDate($row[0]);

    //         // Récupération des entités liées
    //         $societe = Societe::findOrFail($row[3]);
    //         $titre = Titre::findOrFail($row[6]);
    //         $essence = Essence::findOrFail($row[7]);
    //         $forme = Forme::findOrFail($row[8]);
    //         $conditionnement = Conditionnemment::findOrFail($row[9]);
    //         $type = Type::findOrFail($row[10]);

    //         // Vérification de la relation entre forme et type
    //         $this->verifierRelationFormeType($forme, $type);

    //         // Vérification et mise à jour de FormeEssence
    //         $this->updateFormeEssence($essence->id, $forme->id, $type->id);

    //         // Vérification du volume disponible
    //         $volumeDisponible = $this->verifierVolumeDisponible($titre, $essence);

    //         // Déterminer le volume à utiliser pour cette transaction
    //         // Ici, vous pouvez ajouter une logique pour déterminer le volume à partir du fichier Excel
    //         // ou utiliser le volume restant comme dans le code original
    //         $volumeTransaction = $volumeDisponible->pivot->VolumeRestant;

    //         // Créer la transaction
    //         $transaction = Transaction::create([
    //             'date' => $date,
    //             'exercice' => $row[1],
    //             'numero' => $row[2],
    //             'societe_id' => $societe->id,
    //             'destination' => strtoupper($row[4]),
    //             'pays' => strtoupper($row[5]),
    //             'titre_id' => $titre->id,
    //             'essence_id' => $essence->id,
    //             'conditionnemment_id' => $conditionnement->id,
    //             'volume' => $volumeTransaction
    //         ]);

    //         // Mettre à jour le volume restant
    //         $titre->essence()->updateExistingPivot($essence->id, [
    //             'VolumeRestant' => DB::raw("VolumeRestant - {$volumeTransaction}")
    //         ]);

    //         DB::commit();
    //         $this->successCount++;
    //         return $transaction;

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $this->errorCount++;
    //         $this->errors[] = [
    //             'ligne' => $this->rowNumber,
    //             'erreur' => $e->getMessage(),
    //             'donnees' => $row
    //         ];
    //         Log::error('Erreur importation transaction:', [
    //             'message' => $e->getMessage(),
    //             'ligne' => $this->rowNumber,
    //             'donnees' => $row
    //         ]);
    //         return null;
    //     }
    // }

    /**
     * Parse une date qui peut être au format Excel ou au format texte
     */
    private function parseDate($dateValue)
    {
        try {
            // Si la date est déjà au format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
                return Carbon::createFromFormat('Y-m-d', $dateValue);
            }
            // Si c'est un nombre (format Excel)
            if (is_numeric($dateValue)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
            }
            // Tentative de parse générique
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            throw new \Exception('Format de date invalide. Utilisez le format AAAA-MM-JJ');
        }
    }

    /**
     * Vérifie la relation entre forme et type
     */
    private function verifierRelationFormeType(Forme $forme, Type $type)
    {
        if ($type->forme_id != $forme->id) {
            throw new \Exception("Le type {$type->designation} n'est pas compatible avec la forme {$forme->designation}");
        }
    }

    /**
     * Vérifie le volume disponible pour un titre et une essence
     */
    private function verifierVolumeDisponible(Titre $titre, Essence $essence)
    {
        // Recherche de la relation en utilisant les noms plutôt que les IDs
        $volumeDisponible = $titre->essence()
            ->where('essences.id', $essence->id)
            ->first();

        if (!$volumeDisponible) {
            throw new \Exception("Aucune relation trouvée entre le titre '{$titre->nom}' et l'essence '{$essence->nom_local}'. Veuillez vérifier que cette relation existe dans la table pivot.");
        }

        if ($volumeDisponible->pivot->VolumeRestant <= 0) {
            throw new \Exception("Volume insuffisant pour le titre '{$titre->nom}' et l'essence '{$essence->nom_local}' (Volume restant: 0)");
        }

        return $volumeDisponible;
    }

    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            DB::beginTransaction();

            // Recherche des entités par ID ou par nom
            $societe = Societe::findOrFail($row[3]);

            // Recherche du titre
            $titre = Titre::where('id', $row[6])->firstOrFail();

            // Recherche de l'essence
            $essence = Essence::where('id', $row[7])->firstOrFail();

            $forme = Forme::findOrFail($row[8]);
            $conditionnement = Conditionnemment::findOrFail($row[9]);
            $type = Type::findOrFail($row[10]);

            // Vérification de la relation entre forme et type
            $this->verifierRelationFormeType($forme, $type);

            // Vérification et mise à jour de FormeEssence
            $this->updateFormeEssence($essence->id, $forme->id, $type->id);

            // Vérification du volume disponible
            $volumeDisponible = $this->verifierVolumeDisponible($titre, $essence);

            // Utiliser le volume spécifié dans le fichier Excel (dernière colonne)
            $volumeTransaction = floatval(str_replace(',', '.', $row[11]));

            if ($volumeTransaction <= 0) {
                throw new \Exception("Le volume de transaction doit être supérieur à 0");
            }

            if ($volumeTransaction > $volumeDisponible->pivot->VolumeRestant) {
                throw new \Exception("Volume demandé ({$volumeTransaction}) supérieur au volume disponible ({$volumeDisponible->pivot->VolumeRestant})");
            }

            // Créer la transaction
            $transaction = Transaction::create([
                'date' => $this->parseDate($row[0]),
                'exercice' => $row[1],
                'numero' => $row[2],
                'societe_id' => $societe->id,
                'destination' => strtoupper($row[4]),
                'pays' => strtoupper($row[5]),
                'titre_id' => $titre->id,
                'essence_id' => $essence->id,
                'conditionnemment_id' => $conditionnement->id,
                'volume' => $volumeTransaction
            ]);

            // Mettre à jour le volume restant
            $titre->essence()->updateExistingPivot($essence->id, [
                'VolumeRestant' => DB::raw("VolumeRestant - {$volumeTransaction}")
            ]);

            DB::commit();
            $this->successCount++;
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            $this->errors[] = [
                'ligne' => $this->rowNumber,
                'erreur' => $e->getMessage(),
                'donnees' => $row
            ];
            Log::error('Erreur importation transaction:', [
                'message' => $e->getMessage(),
                'ligne' => $this->rowNumber,
                'donnees' => $row
            ]);
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
            'error_count' => $this->errorCount,
            'errors' => $this->errors
        ];
    }
}
