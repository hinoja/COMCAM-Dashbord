<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\FormeEssence;
use App\Models\Conditionnemment;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddTransaction extends Component
{
    // use LivewireAlert;

    public $showSuccessAlert = false;
    public $showDepassementModal = false;
    public $depassementValue;
    public $volumeRestantGrume;
    public $volumeRestantDebite;

    public $formeTitre; // Nouvelle propriété
    public $volumeRestantTitre; // Nouvelle propriété
    // Propriétés du formulaire
    public $date = '';
    public $exercice = 2024;
    public $numero = 0;
    public $forme_id;
    public $type_id;
    public $titre_id = 1;
    public $titres = [];
    public $essence_id = 1;
    public $conditionnemment_id = 1;
    public $societe_id = 1;
    public $pays = '';
    public $destination = '';
    public $volume = 0;
    public $depassement = false;
    public $filteredTypes = []; // Nouvelle propriété pour les types filtrés

    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * Initialisation du composant
     */
    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->updateFilteredTypes(); // Initialiser les types filtrés
    }
    public function updatedEssenceId($value)
    {
        // Rafraîchir les titres lorsque l'essence change
        $this->titres = Titre::whereHas('essence', function ($query) use ($value) {
            $query->where('essences.id', $value);
        })
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->unique('nom');
    }

    /**
     * Règles de validation des champs
     */
    protected function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'exercice' => ['required', 'int', 'digits:4', 'min:2024'],
            'numero' => ['required', 'numeric', 'min:0'],
            'titre_id' => [
                'required',
                'int',
                'exists:titres,id',
                function ($attribute, $value, $fail) {
                    $valid = Titre::where('id', $value)
                        ->whereHas('essence', function ($query) {
                            $query->where('essences.id', $this->essence_id);
                        })
                        ->exists();

                    // if (!$valid) {
                    //     $fail('Ce titre ne correspond pas à l\'essence sélectionnée.');
                    // }
                }
            ],
            'type_id' => [
                'required',
                'exists:types,id',
                function ($attribute, $value, $fail) {
                    // For Grume (forme_id = 1)
                    if ($this->forme_id == 1 && $value != 1) {
                        $fail('Pour la forme Grume, seul le type Non Applicable est autorisé.');
                    }

                    // For Débité (forme_id = 2)
                    if ($this->forme_id == 2 && !in_array($value, [2, 3, 4, 5])) {
                        $fail('Pour la forme Débité, seuls les types 2, 3, 4, 5 sont autorisés.');
                    }
                }
            ],
            'essence_id' => ['required', 'int', 'exists:essences,id'],
            'forme_id' => ['required', 'int', 'exists:formes,id'],
            'conditionnemment_id' => ['required', 'int', 'exists:conditionnemments,id'],
            'societe_id' => ['required', 'int', 'exists:societes,id'],
            'pays' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'volume' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Mise à jour lorsque forme_id change
     */
    public function updatedFormeId($value)
    { // Reset type_id when forme changes
        $this->type_id = null;

        // If forme_id is 1, set type_id to 1 (Non applicable)
        if ($value == 1) {
            $this->type_id = 1;
        } elseif ($value == 2) { // Si Débité est sélectionné
            $this->type_id = null; // Réinitialiser le type
        }
        $this->updateFilteredTypes(); // Mettre à jour les types filtrés
    }

    /**
     * Met à jour les types filtrés en fonction de forme_id
     */
    private function updateFilteredTypes()
    {
        $this->filteredTypes = Type::query()
            ->when($this->forme_id == 1, function ($query) {
                return $query->where('id', 1);
            })
            ->when($this->forme_id == 2, function ($query) {
                return $query->whereIn('id', [2, 3, 4, 5]);
            })
            ->get(['id', 'code']);
    }

    /**
     * Gestion de la soumission du formulaire
     */
    // public function save()
    // {
    //     try {
    //         $this->validate();

    //         // Récupérer le titre associé
    //         $titre = Titre::where('id', $this->titre_id)
    //             ->whereHas('essence', function ($query) {
    //                 $query->where('essences.id', $this->essence_id);
    //             })
    //             ->first();

    //         if (!$titre) {
    //             $this->addError('titre_id', 'Combinaison titre/essence invalide');
    //             return;
    //         }

    //         // Créer la transaction
    //         $transaction = new Transaction($this->prepareTransactionData());
    //         // ✅ Calculer D'ABORD, mettre à jour FormeEssence ENSUITE
    //       $nouveauVolumeRestant = $this->calculateDepassement($transaction, $titre);

    //         // Calculer le nouveau volume restant
    //         $volumeRestant = $this->getVolumeRestant($titre);
    //         // $nouveauVolumeRestant = $volumeRestant - $transaction->volume;
    //         // $nouveauVolumeRestant = $this->calculateDepassement($transaction, $titre);
    //         // Vérifier s'il y a dépassement
    //         if ($nouveauVolumeRestant < 0) {
    //             // Gérer le dépassement
    //             $this->handleDepassementWarning($transaction, $nouveauVolumeRestant);
    //             return;
    //         }

    //         // Sauvegarder la transaction
    //         $transaction->save();

    //         // Mettre à jour le volume restant dans la table pivot
    //         $pivotEntry = $titre->essence()
    //             ->where('essences.id', $this->essence_id)
    //             ->first();

    //             //  Mettre à jour ou créer l'entrée dans FormeEssence
    //         $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);


    //         if ($pivotEntry) {
    //             $titre->essence()->updateExistingPivot($this->essence_id, [
    //                 'VolumeRestant' => $nouveauVolumeRestant
    //             ]);
    //         }

    //         $this->resetForm();
    //         $this->showSuccessAlert = true;

    //         // Rafraîchir le composant
    //         // $this->dispatch('refreshComponent');
    //         $this->dispatch('redirectToList');
    //     } catch (\Exception $e) {
    //         $this->addError('save', "Erreur lors de l'enregistrement : " . $e->getMessage());
    //     }
    // }
    public function save()
    {
        try {
            $this->validate();

            // Récupérer le titre associé
            $titre = Titre::where('id', $this->titre_id)
                ->whereHas('essence', function ($query) {
                    $query->where('essences.id', $this->essence_id);
                })
                ->first();

            if (!$titre) {
                $this->addError('titre_id', 'Combinaison titre/essence invalide');
                return;
            }

            // Créer la transaction
            $transaction = new Transaction($this->prepareTransactionData());

            // Calculer le nouveau volume restant AVANT de modifier FormeEssence
            $nouveauVolumeRestant = $this->calculateDepassement($transaction, $titre);

            // Vérifier s'il y a dépassement
            if ($nouveauVolumeRestant < 0) {
                $this->handleDepassementWarning($transaction, $nouveauVolumeRestant);
                return;
            }

            // Sauvegarder la transaction
            $transaction->save();

            // Mettre à jour FormeEssence APRÈS le calcul
            $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);

            // Mettre à jour le volume restant dans la table pivot
            $pivotEntry = $titre->essence()
                ->where('essences.id', $this->essence_id)
                ->first();

            if ($pivotEntry) {
                $titre->essence()->updateExistingPivot($this->essence_id, [
                    'VolumeRestant' => $nouveauVolumeRestant
                ]);
            }

            $this->resetForm();
            $this->showSuccessAlert = true;
            $this->dispatch('redirectToList');
        } catch (\Exception $e) {
            $this->addError('save', "Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }
    /**
     * Prépare les données de la transaction
     */
    private function prepareTransactionData(): array
    {
        return [
            'date' => $this->date,
            'exercice' => $this->exercice,
            'numero' => $this->numero,
            'societe_id' => $this->societe_id,
            'destination' => strtoupper($this->destination),
            'pays' => strtoupper($this->pays),
            'titre_id' => $this->titre_id,
            'essence_id' => $this->essence_id,
            'conditionnemment_id' => $this->conditionnemment_id,
            'volume' => (float)$this->volume,
        ];
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
     * Récupère le titre associé avec vérification de l'essence
     */
    private function getRelatedTitre(Transaction $transaction)
    {
        return Titre::where('nom', $transaction->titre->nom)
            ->whereHas('essence', function ($query) use ($transaction) {
                $query->where('essences.id', $transaction->essence_id);
            })
            ->first();
    }

    public function getFilteredTypesProperty()
    {
        $filteredTypes = Type::query();

        foreach ($this->details as $index => $detail) {
            if ($this->isDebiteForm($index)) {
                $filteredTypes->whereIn('id', [2, 3, 4, 5]);
                break;
            } else if ($this->isGrumeForm($index)) {
                $filteredTypes->whereIn('id', [1]);
                break;
            }
        }

        return $filteredTypes->get(['id', 'code']);
    }
    private function isDebiteForm($index)
    {
        return $this->forme_id == 2; // ID 2 = Débité
    }
    private function isGrumeForm($index)
    {
        return $this->forme_id == 1; // ID 1 = Grume
    }
    /**
     * Calcule le dépassement selon les règles métier
     */
    // private function calculateDepassement(Transaction $transaction, Titre $titre): float
    // {
    //     $volumeRestant = $this->getVolumeRestant($titre);

    //     // Récupérer l'essence associée à la transaction
    //     $essence = Essence::find($this->essence_id);

    //     // Récupérer forme_id et type_id à partir de formeEssence si disponible
    //     $formeId = null;
    //     $typeId = null;

    //     if ($essence && $essence->formeEssence) {
    //         $formeId = $essence->formeEssence->forme_id;
    //         $typeId = $essence->formeEssence->type_id;
    //     }

    //     $formeTypeTitre = $this->getFormeType($formeId, $typeId);
    //     $formeTypeTransaction = $this->getFormeType($transaction->forme_id, $transaction->type_id);

    //     // Cas 1 : Mêmes caractéristiques
    //     if ($formeTypeTitre === $formeTypeTransaction) {
    //         return $volumeRestant - $transaction->volume;
    //     }

    //     // Cas 2 : Conversion depuis des grumes
    //     if ($formeTypeTitre === 'Grume') {
    //         return $this->handleGrumeConversion($transaction, $formeTypeTransaction, $volumeRestant);
    //     }

    //     // Cas 3 : Conversion vers des grumes
    //     if ($formeTypeTransaction === 'Grume') {
    //         return $this->handleReverseGrumeConversion($titre, $formeTypeTitre, $volumeRestant, $transaction->volume);
    //     }

    //     return $volumeRestant - $transaction->volume;
    // }
    private function calculateDepassement(Transaction $transaction, Titre $titre): float
    {
        $volumeRestant = $this->getVolumeRestant($titre);

        // ✅ On lit directement depuis le formulaire Livewire
        $formeIdTitre   = $this->getFormeTitre($titre); // forme stockée sur le titre
        $formeIdTransaction = (int) $this->forme_id;    // forme sélectionnée dans le formulaire
        $typeIdTransaction  = (int) $this->type_id;     // type sélectionné dans le formulaire

        $formeTypeTitre       = $this->getFormeType($formeIdTitre['forme_id'], $formeIdTitre['type_id']);
        $formeTypeTransaction = $this->getFormeType($formeIdTransaction, $typeIdTransaction);

        // Cas 1 : Même forme → soustraction directe
        if ($formeTypeTitre === $formeTypeTransaction) {
            return $volumeRestant - $transaction->volume;
        }

        // Cas 2 : Titre en Grume, transaction en Débité → convertir Débité en Grume
        if (str_starts_with($formeTypeTitre, 'Grume')) {
            return $this->handleGrumeConversion($transaction, $formeTypeTransaction, $volumeRestant);
        }

          // Cas 3 : Titre en Débité, transaction en Grume → convertir Grume en Débité
    if (str_starts_with($formeTypeTransaction, 'Grume')) {
        // Titre $titre, string $formeType, float $volumeRestant, float $volume
        return $this->handleReverseGrumeConversion($formeTypeTitre, $volumeRestant, $transaction->volume); // ✅ décommenté
    }

        return $volumeRestant - $transaction->volume;
    }

    /**
     * ✅ Nouvelle méthode helper — récupère la forme/type associée au titre
     */
    // private function getFormeTitre(Titre $titre): array
    // {
    //     $essence = Essence::find($this->essence_id);

    //     if ($essence && $essence->formeEssence) {
    //         return [
    //             'forme_id' => $essence->formeEssence->forme_id,
    //             'type_id'  => $essence->formeEssence->type_id,
    //         ];
    //     }

    //     // Fallback : on utilise ce qui est dans le formulaire
    //     return [
    //         'forme_id' => (int) $this->forme_id,
    //         'type_id'  => (int) $this->type_id,
    //     ];
    // }
    private function getFormeTitre(Titre $titre): array
    {
        // ✅ Lire depuis la table pivot essence_titre (source de vérité)
        $pivotEntry = $titre->essence()
            ->where('essences.id', $this->essence_id)
            ->first();

        if ($pivotEntry && isset($pivotEntry->pivot->forme_id)) {
            return [
                'forme_id' => (int) $pivotEntry->pivot->forme_id,
                'type_id'  => (int) $pivotEntry->pivot->type_id,
            ];
        }

        // Fallback : formeEssence en BDD
        $essence = Essence::find($this->essence_id);
        if ($essence && $essence->formeEssence) {
            return [
                'forme_id' => $essence->formeEssence->forme_id,
                'type_id'  => $essence->formeEssence->type_id,
            ];
        }

        // Dernier recours
        return [
            'forme_id' => (int) $this->forme_id,
            'type_id'  => (int) $this->type_id,
        ];
    }
    /**
     * Gère la conversion depuis des grumes
     */
    private function handleGrumeConversion(Transaction $transaction, string $formeType, float $volumeRestant): float
    {
        return match ($formeType) {
            'Débité5N' => $volumeRestant - ($transaction->volume * 2.5),
            'Débité6.1', 'Débité6.2' => $volumeRestant - ($transaction->volume * 1.25),
            default => $volumeRestant - $transaction->volume
        };
    }

    /**
     * Gère la conversion vers des grumes
     */
    private function handleReverseGrumeConversion(string $formeType, float $volumeRestant, float $volume): float
    {
        return match ($formeType) {
            'Débité5N' => $volumeRestant - ($volume * 0.4),
            'Débité6.1', 'Débité6.2' => $volumeRestant - ($volume * 0.8),
            default => $volumeRestant - $volume
        };
    }

    /**
     * Détermine le volume restant initial
     */
    private function getVolumeRestant(Titre $titre): float
    {
        // Récupérer l'essence associée à la transaction en cours
        $essence = Essence::find($this->essence_id);
        if (!$essence) {
            return 0.0; // Retourner 0 si l'essence n'est pas trouvée
        }

        // Récupérer l'entrée dans la table pivot pour ce titre et cette essence
        $pivotEntry = $titre->essence()
            ->where('essences.id', $this->essence_id)
            ->first();

        if (!$pivotEntry) {
            return 0.0; // Retourner 0 si aucune entrée n'est trouvée
        }

        // Vérifier si des transactions existent pour ce titre et cette essence
        $hasTransactions = Transaction::where('titre_id', $titre->id)
            ->where('essence_id', $this->essence_id)
            ->exists();

        // Retourner le volume restant ou le volume initial
        return $hasTransactions
            ? (float)($pivotEntry->pivot->VolumeRestant ?? 0.0)
            : (float)($pivotEntry->pivot->volume ?? 0.0);
    }

    /**
     * Génère la clé de type de forme (ex: Grume5N)
     */
    private function getFormeType(?int $formeId, ?int $typeId): string
    {
        if ($formeId === null || $typeId === null) {
            return "Inconnu"; // Valeur par défaut si forme_id ou type_id est null
        }

        $forme = Forme::find($formeId);
        $type = Type::find($typeId);

        if (!$forme || !$type) {
            return "Inconnu"; // Valeur par défaut si forme ou type n'est pas trouvé
        }

        return $forme->designation . ($type->code ?? '');
    }
    private function calculateVolumeRestantDebite(Titre $titre, Transaction $transaction): float
    {
        $volumeRestant = $this->getVolumeRestant($titre);
        $conversionFactor = 2.5; // Ajustez selon vos règles
        return ($volumeRestant / $conversionFactor) - $transaction->volume;
    }
    private function calculateVolumeRestantGrume(Titre $titre, Transaction $transaction): float
    {
        $volumeRestant = $this->getVolumeRestant($titre);
        $conversionFactor = 0.4; // Ajustez selon vos règles
        return ($volumeRestant * $conversionFactor) - $transaction->volume;
    }
    /**
     * Gère l'alerte de dépassement
     */

    private function handleDepassementWarning(Transaction $transaction, float $depassement): void
    {
        $titre = Titre::find($this->titre_id);
        $this->depassementValue = abs($depassement);

        // Calcul du volume restant actuel (avant la transaction en cours)
        $volumeRestantActuel = $this->getVolumeRestant($titre);

        // Utiliser la forme sélectionnée dans le formulaire, pas celle stockée
        $formeId = $this->forme_id;
        $typeId = $this->type_id;

        // Calculer les volumes équivalents selon la forme de la transaction
        if ($formeId == 1) { // Transaction en Grume
            $this->volumeRestantGrume = $volumeRestantActuel;
            // Conversion vers Débité selon le type sélectionné
            $this->volumeRestantDebite = $this->convertirGrumeVersDebite($volumeRestantActuel, $typeId);
        } else { // Transaction en Débité
            $this->volumeRestantDebite = $volumeRestantActuel;
            // Conversion vers Grume selon le type sélectionné
            $this->volumeRestantGrume = $this->convertirDebiteVersGrume($volumeRestantActuel, $typeId);
        }

        $this->showDepassementModal = true;

        // Passer les données au modal via l'événement
        $this->dispatch('showDepassementModal', [
            'depassementValue' => $this->depassementValue,
            'volumeRestantGrume' => $this->volumeRestantGrume,
            'volumeRestantDebite' => $this->volumeRestantDebite,
            'formeTransaction' => $formeId == 1 ? 'Grume' : 'Débité',
            'typeTransaction' => $this->getTypeCode($typeId)
        ]);
    }

    /**
     * Convertit un volume Grume vers Débité selon le type
     */
    private function convertirGrumeVersDebite(float $volumeGrume, int $typeId): float
    {
        return match ($typeId) {
            2 => $volumeGrume * 0.4,  // 5N
            3 => $volumeGrume * 0.8, // 6.1
            4 => $volumeGrume * 0.8, // 6.2
            5 => $volumeGrume * 1.0,  // PS
            default => $volumeGrume * 0.4 // Par défaut 5N
        };
    }

    /**
     * Convertit un volume Débité vers Grume selon le type
     */
    private function convertirDebiteVersGrume(float $volumeDebite, int $typeId): float
    {
        return match ($typeId) {
            2 => $volumeDebite * 2.5,  // 5N
            3 => $volumeDebite * 1.25,  // 6.1
            4 => $volumeDebite * 1.25,  // 6.2
            5 => $volumeDebite * 1.0,  // PS
            default => $volumeDebite * 2.5 // Par défaut 5N
        };
    }

    /**
     * Récupère le code du type
     */
    private function getTypeCode(int $typeId): string
    {
        $type = Type::find($typeId);
        return $type ? $type->code : 'Non défini';
    }

    /**
     * Finalise l'enregistrement
     */
    private function finalizeTransaction(Transaction $transaction, Titre $titre, float $depassement): void
    {
        // Sauvegarder la transaction
        $transaction->save();

        // Mettre à jour la table pivot essence_titre avec le nouveau volume restant
        $pivotEntry = $titre->essence()
            ->where('essences.id', $this->essence_id)
            ->first();

        if ($pivotEntry) {
            $titre->essence()->updateExistingPivot($this->essence_id, [
                'VolumeRestant' => $depassement
            ]);
        }

        $this->resetForm();
        $this->showSuccessAlert = true; // Activer l'alerte de succès
        $this->dispatch('redirectToList');
    }
    // Ajouter cette méthode pour la confirmation
    // public function confirmSaveWithDepassement()
    // {
    //     try {
    //         $this->validate();

    //         // Créer la transaction sans forme_id et type_id
    //         $transaction = new Transaction($this->prepareTransactionData());
    //         $titre = Titre::find($this->titre_id);

    //         // Mettre à jour ou créer l'entrée dans FormeEssence
    //         $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);

    //         // Mettre à jour la table pivot essence_titre avec le nouveau volume restant
    //         $pivotEntry = $titre->essence()
    //             ->where('essences.id', $this->essence_id)
    //             ->first();

    //         if ($pivotEntry) {
    //             $titre->essence()->updateExistingPivot($this->essence_id, [
    //                 'VolumeRestant' => -$this->depassementValue
    //             ]);
    //         }

    //         $transaction->save();

    //         $this->closeDepassementModal();
    //         $this->resetForm();
    //         $this->showSuccessAlert = true;
    //         $this->dispatch('redirectToList');


    //         // Rafraîchir le composant
    //         // $this->dispatch('refreshComponent');
    //         $this->dispatch('redirectToList');
    //     } catch (\Exception $e) {
    //         $this->addError('save', "Erreur lors de l'enregistrement : " . $e->getMessage());
    //     }
    // }
    public function confirmSaveWithDepassement()
    {
        try {
            $this->validate();

            $transaction = new Transaction($this->prepareTransactionData());
            $titre = Titre::find($this->titre_id);

            // ✅ Calculer AVANT updateFormeEssence
            $nouveauVolumeRestant = $this->calculateDepassement($transaction, $titre);

            // ✅ Sauvegarder la transaction
            $transaction->save();

            // ✅ updateFormeEssence APRÈS le calcul
            $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);

            $pivotEntry = $titre->essence()
                ->where('essences.id', $this->essence_id)
                ->first();

            if ($pivotEntry) {
                $titre->essence()->updateExistingPivot($this->essence_id, [
                    'VolumeRestant' => $nouveauVolumeRestant // ✅ valeur recalculée
                ]);
            }

            $this->closeDepassementModal();
            $this->resetForm();
            $this->showSuccessAlert = true;
            $this->dispatch('redirectToList');
        } catch (\Exception $e) {
            $this->addError('save', "Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }
    /**
     * Méthode pour fermer le modal
     */
    public function closeDepassementModal()
    {
        $this->showDepassementModal = false;
        $this->depassementValue = null;
        $this->volumeRestantGrume = null;
        $this->volumeRestantDebite = null;
        $this->dispatch('hideDepassementModal');
    }

    public function showDepassementModal($depassement)
    {
        $this->depassementValue = abs($depassement);
        $this->calculateVolumesRestants();
        $this->showDepassementModal = true;

        // Passer les données au modal via l'événement
        $this->dispatch('showDepassementModal', [
            'depassementValue' => $this->depassementValue,
            'volumeRestantGrume' => $this->volumeRestantGrume,
            'volumeRestantDebite' => $this->volumeRestantDebite
        ]);
    }

    private function calculateVolumesRestants()
    {
        $titre = Titre::find($this->titre_id);
        if (!$titre) return;

        $volumeRestantActuel = $this->getVolumeRestant($titre);

        // Utiliser la forme sélectionnée dans le formulaire
        $formeId = $this->forme_id;
        $typeId = $this->type_id;

        if ($formeId == 1) { // Transaction en Grume
            $this->volumeRestantGrume = $volumeRestantActuel;
            $this->volumeRestantDebite = $this->convertirGrumeVersDebite($volumeRestantActuel, $typeId);
        } else { // Transaction en Débité
            $this->volumeRestantDebite = $volumeRestantActuel;
            $this->volumeRestantGrume = $this->convertirDebiteVersGrume($volumeRestantActuel, $typeId);
        }
    }

    /**
     * Réinitialise le formulaire
     */
    private function resetForm(): void
    {
        $this->reset([
            'date',
            'exercice',
            'numero',
            'titre_id',
            'type_id',
            'essence_id',
            'forme_id',
            'conditionnemment_id',
            'societe_id',
            'pays',
            'destination',
            'volume'
        ]);
    }

    /**
     * Rendu de la vue
     */
    public function render()
    {
        $types = Type::query(['id', 'code']);

        if ($this->forme_id == 2) {
            $types->whereIn('id', [2, 3, 4, 5]);
        }

        return view('livewire.add-transaction', [
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
            'titres' =>  $this->essence_id ? Titre::whereHas('essence', function ($query) {
                $query->where('essences.id', $this->essence_id);
            })
                ->orderBy('nom')
                ->get(['id', 'nom'])
                ->unique('nom')
                : collect(),
            // 'titres' => $this->titres,
            'conditionnements' => Conditionnemment::all(['id', 'code']),
            'societes' => Societe::all(['id', 'acronym']),
            'filteredTypes' => $this->filteredTypes, // Passer les types filtrés à la vue
        ]);
    }
}
