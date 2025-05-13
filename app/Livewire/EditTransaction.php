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
use Illuminate\Support\Facades\DB;

class EditTransaction extends Component
{
    public $transactionId;
    public $showSuccessAlert = false;
    public $showDepassementModal = false;
    public $depassementValue;
    public $volumeRestantGrume;
    public $volumeRestantDebite;
    public $originalVolume; // Pour stocker le volume original

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
    public $filteredTypes = []; // Pour les types filtrés

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'date' => 'required|date',
        'exercice' => 'required|integer',
        'numero' => 'required|integer',
        'societe_id' => 'required|exists:societes,id',
        'destination' => 'required|string',
        'pays' => 'required|string',
        'titre_id' => 'required|exists:titres,id',
        'essence_id' => 'required|exists:essences,id',
        'forme_id' => 'required|exists:formes,id',
        'conditionnemment_id' => 'required|exists:conditionnemments,id',
        'type_id' => 'required|exists:types,id',
        'volume' => 'required|numeric|min:0.01',
    ];

    /**
     * Initialisation du composant
     */
    public function mount($id)
    {
        $transaction = Transaction::with([
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
                    $query->with(['forme', 'type']);
                }]);
            },
            'titre',
            'societe',
            'conditionnemment'
        ])->findOrFail($id);

        $this->transactionId = $transaction->id;
        $this->date = $transaction->date;
        $this->exercice = $transaction->exercice;
        $this->numero = $transaction->numero;
        $this->societe_id = $transaction->societe_id;
        $this->destination = $transaction->destination;
        $this->pays = $transaction->pays;
        $this->titre_id = $transaction->titre_id;
        $this->essence_id = $transaction->essence_id;
        $this->conditionnemment_id = $transaction->conditionnemment_id;
        $this->volume = $transaction->volume;
        $this->originalVolume = $transaction->volume;

        // Récupérer forme_id et type_id depuis FormeEssence
        if ($transaction->essence && $transaction->essence->formeEssence) {
            $this->forme_id = $transaction->essence->formeEssence->forme_id;
            $this->type_id = $transaction->essence->formeEssence->type_id;
        }

        // Charger les titres associés à cette essence
        $this->titres = Titre::whereHas('essence', function ($query) {
            $query->where('essences.id', $this->essence_id);
        })
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->unique('nom');

        $this->updateFilteredTypes();
    }

    /**
     * Mise à jour des types filtrés en fonction de la forme sélectionnée
     */
    public function updateFilteredTypes()
    {
        if ($this->forme_id == 1) { // Grume
            $this->filteredTypes = Type::where('id', 1)->get(['id', 'code']);
            $this->type_id = 1; // Définir automatiquement à 1 pour Grume
        } elseif ($this->forme_id == 2) { // Débité
            $this->filteredTypes = Type::whereIn('id', [2, 3, 4, 5])->get(['id', 'code']);
        } else {
            $this->filteredTypes = Type::all(['id', 'code']);
        }
    }

    /**
     * Mise à jour de l'essence_id
     */
    public function updatedEssenceId($value)
    {
        // Rafraîchir les titres lorsque l'essence change
        $this->titres = Titre::whereHas('essence', function ($query) use ($value) {
            $query->where('essences.id', $value);
        })
            ->orderBy('nom')
            ->get(['id', 'nom'])
            ->unique('nom');

        // Réinitialiser le titre_id si nécessaire
        if ($this->titres->isNotEmpty()) {
            $titreExists = $this->titres->where('id', $this->titre_id)->count() > 0;
            if (!$titreExists) {
                $firstTitre = $this->titres->first();
                $this->titre_id = $firstTitre ? $firstTitre->id : null;
            }
        } else {
            $this->titre_id = null;
        }
    }

    /**
     * Mise à jour de la forme_id
     */
    public function updatedFormeId()
    {
        $this->updateFilteredTypes();
    }

    /**
     * Préparation des données de la transaction
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
     * Récupère le titre associé à la transaction
     */
    private function getRelatedTitre(Transaction $transaction)
    {
        return Titre::where('id', $transaction->titre_id)
            ->whereHas('essence', function ($query) {
                $query->where('essences.id', $this->essence_id);
            })
            ->first();
    }

    /**
     * Calcule le dépassement de volume
     */
    private function calculateDepassement(Transaction $transaction, Titre $titre): float
    {
        // Récupérer le volume restant actuel
        $volumeRestant = $this->getVolumeRestant($titre);

        // Calculer la différence entre le nouveau volume et l'ancien volume
        $volumeDifference = $transaction->volume - $this->originalVolume;

        // Calculer le nouveau volume restant
        return $volumeRestant - $volumeDifference;
    }

    /**
     * Récupère le volume restant pour un titre
     */
    private function getVolumeRestant(Titre $titre): float
    {
        $pivotEntry = $titre->essence()
            ->where('essences.id', $this->essence_id)
            ->first();

        if ($pivotEntry) {
            return $pivotEntry->pivot->VolumeRestant;
        }

        return 0;
    }

    /**
     * Gestion des dépassements de volume
     */
    private function handleDepassementWarning(float $depassement): void
    {
        $titre = Titre::find($this->titre_id);
        $this->depassementValue = abs($depassement);

        // Calcul du volume restant actuel (avant la transaction en cours)
        $volumeRestantActuel = $this->getVolumeRestant($titre);

        // Récupérer l'essence associée à la transaction
        $essence = Essence::find($this->essence_id);

        // Récupérer la forme depuis FormeEssence
        $formeId = null;
        if ($essence && $essence->formeEssence) {
            $formeId = $essence->formeEssence->forme_id;
        }

        // Selon la forme de l'essence
        if ($formeId == 1) { // Grume
            $this->volumeRestantGrume = $volumeRestantActuel;
            $this->volumeRestantDebite = $this->convertirGrumeEnDebite($volumeRestantActuel);
        } else { // Débité
            $this->volumeRestantDebite = $volumeRestantActuel;
            $this->volumeRestantGrume = $this->convertirDebiteEnGrume($volumeRestantActuel);
        }

        $this->showDepassementModal = true;

        // Passer les données au modal via l'événement
        $this->dispatch('showDepassementModal', [
            'depassementValue' => $this->depassementValue,
            'volumeRestantGrume' => $this->volumeRestantGrume,
            'volumeRestantDebite' => $this->volumeRestantDebite
        ]);
    }

    /**
     * Finalisation de la transaction
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

        $this->showSuccessAlert = true; // Activer l'alerte de succès
    }

    /**
     * Conversion de volume Grume en Débité
     */
    private function convertirGrumeEnDebite($volume)
    {
        return $volume * 0.7; // Facteur de conversion
    }

    /**
     * Conversion de volume Débité en Grume
     */
    private function convertirDebiteEnGrume($volume)
    {
        return $volume / 0.7; // Facteur de conversion inverse
    }

    /**
     * Fermeture du modal de dépassement
     */
    public function closeDepassementModal()
    {
        $this->showDepassementModal = false;
    }

    /**
     * Confirmation de sauvegarde avec dépassement
     */
    public function confirmSaveWithDepassement()
    {
        try {
            $this->validate();

            // Créer la transaction sans forme_id et type_id
            $transaction = Transaction::findOrFail($this->transactionId);
            $transaction->fill($this->prepareTransactionData());
            $titre = Titre::find($this->titre_id);

            // Mettre à jour ou créer l'entrée dans FormeEssence
            $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);

            // Mettre à jour la table pivot essence_titre avec le nouveau volume restant
            $pivotEntry = $titre->essence()
                ->where('essences.id', $this->essence_id)
                ->first();

            if ($pivotEntry) {
                $titre->essence()->updateExistingPivot($this->essence_id, [
                    'VolumeRestant' => -$this->depassementValue
                ]);
            }

            $transaction->save();

            $this->closeDepassementModal();
            $this->showSuccessAlert = true;

            // Rafraîchir le composant
            $this->dispatch('refreshComponent');
        } catch (\Exception $e) {
            $this->addError('save', "Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    /**
     * Mise à jour de la transaction
     */
    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Récupérer la transaction existante
            $transaction = Transaction::findOrFail($this->transactionId);

            // Le volume original est déjà stocké dans $this->originalVolume

            // Mettre à jour les données de la transaction
            $transaction->fill($this->prepareTransactionData());

            // Récupérer le titre associé
            $titre = Titre::where('id', $this->titre_id)
                ->whereHas('essence', function ($query) {
                    $query->where('essences.id', $this->essence_id);
                })
                ->first();

            // Vérification de la cohérence titre/essence
            if ($titre != $this->getRelatedTitre($transaction)) {
                $this->addError('titre_id', 'Combinaison titre/essence invalide');
                DB::rollBack();
                return;
            }

            // Mettre à jour ou créer l'entrée dans FormeEssence
            $this->updateFormeEssence($this->essence_id, $this->forme_id, $this->type_id);

            // Calcul du dépassement
            $depassement = $this->calculateDepassement($transaction, $titre);

            // Gestion des dépassements négatifs
            if ($depassement < 0) {
                $this->handleDepassementWarning($depassement);
                DB::rollBack();
                return;
            }

            // Finalisation de la transaction
            $this->finalizeTransaction($transaction, $titre, $depassement);

            DB::commit();

            session()->flash('success', 'Transaction mise à jour avec succès!');
            return redirect()->route('admin.transaction.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Rendu de la vue
     */
    public function render()
    {
        return view('livewire.edit-transaction', [
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
            'titres' => $this->titres,
            'conditionnements' => Conditionnemment::all(['id', 'code']),
            'societes' => Societe::all(['id', 'acronym']),
            'filteredTypes' => $this->filteredTypes,
        ]);
    }
}
