<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use Livewire\Component;
use App\Models\Transaction;
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
        $this->titres = Titre::where('essence_id', $value)
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
                        ->where('essence_id', $this->essence_id)
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
    public function save()
    {

        $this->validate();
        $titre = Titre::where('id', $this->titre_id)->where('essence_id', $this->essence_id)->first();

        $transaction = new Transaction($this->prepareTransactionData());
        // dd($this->titre_id, $this->essence_id,$transaction );

        // Vérification de la cohérence titre/essence
        if ($titre != $this->getRelatedTitre($transaction)) {
            $this->addError('titre_id', 'Combinaison titre/essence invalide');
            return;
        }

        // Calcul du dépassement
        $depassement = $this->calculateDepassement($transaction, $titre);

        // Gestion des dépassements négatifs
        if ($depassement < 0) {
            $this->handleDepassementWarning($transaction, $depassement);
            return;
        }

        // Finalisation de la transaction
        $this->finalizeTransaction($transaction, $titre, $depassement);
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
            'forme_id' => $this->forme_id,
            'conditionnemment_id' => $this->conditionnemment_id,
            'type_id' => $this->type_id,
            'volume' => (float)$this->volume,
        ];
    }

    /**
     * Récupère le titre associé avec vérification de l'essence
     */
    private function getRelatedTitre(Transaction $transaction)
    {
        return Titre::where('nom', $transaction->titre->nom)
            ->where('essence_id', $transaction->essence_id)
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
    private function calculateDepassement(Transaction $transaction, Titre $titre): float
    {
        $volumeRestant = $this->getVolumeRestant($titre);
        $formeTypeTitre = $this->getFormeType($titre->forme_id, $titre->type_id);
        $formeTypeTransaction = $this->getFormeType($transaction->forme_id, $transaction->type_id);

        // Cas 1 : Mêmes caractéristiques
        if ($formeTypeTitre === $formeTypeTransaction) {
            return $volumeRestant - $transaction->volume;
        }

        // Cas 2 : Conversion depuis des grumes
        if ($formeTypeTitre === 'Grume') {
            return $this->handleGrumeConversion($transaction, $formeTypeTransaction, $volumeRestant);
        }

        // Cas 3 : Conversion vers des grumes
        if ($formeTypeTransaction === 'Grume') {
            return $this->handleReverseGrumeConversion($titre, $formeTypeTitre, $volumeRestant, $transaction->volume);
        }

        return $volumeRestant - $transaction->volume;
    }

    /**
     * Gère la conversion depuis des grumes
     */
    private function handleGrumeConversion(Transaction $transaction, string $formeType, float $volumeRestant): float
    {
        return match ($formeType) {
            'Débité5N' => $volumeRestant - ($transaction->volume * 2.5),
            'Débité6.1', 'Débité6.2' => $volumeRestant - ($transaction->volume * 1.5),
            default => $volumeRestant - $transaction->volume
        };
    }

    /**
     * Gère la conversion vers des grumes
     */
    private function handleReverseGrumeConversion(Titre $titre, string $formeType, float $volumeRestant, float $volume): float
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
        return Transaction::where('titre_id', $titre->id)
            ->where('essence_id', $titre->essence_id)
            ->exists()
            ? $titre->VolumeRestant
            : $titre->volume;
    }

    /**
     * Génère la clé de type de forme (ex: Grume5N)
     */
    private function getFormeType(int $formeId, int $typeId): string
    {
        return Forme::find($formeId)->designation . Type::find($typeId)->code;
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

        // Selon la forme du titre
        if ($titre->forme_id == 1) { // Grume
            $this->volumeRestantGrume = $volumeRestantActuel;
            $this->volumeRestantDebite = $this->convertirGrumeEnDebite($volumeRestantActuel);
        } else { // Débité
            $this->volumeRestantDebite = $volumeRestantActuel;
            $this->volumeRestantGrume = $this->convertirDebiteEnGrume($volumeRestantActuel);
        }

        $this->showDepassementModal = true;
        $this->dispatch('showDepassementModal');
    }
    private function convertirGrumeEnDebite(float $volumeGrume): float
    {
        // Exemple : coefficient de conversion de Grume à Débité (5N) = 0.4
        return $volumeGrume * 0.4;
    }

    private function convertirDebiteEnGrume(float $volumeDebite): float
    {
        // Exemple : coefficient de conversion de Débité (5N) à Grume =2.5
        return $volumeDebite * 2.5;
    }

    /**
     * Finalise l'enregistrement
     */
    private function finalizeTransaction(Transaction $transaction, Titre $titre, float $depassement): void
    {
        $transaction->save();
        $titre->update(['VolumeRestant' => $depassement]);
        $this->resetForm();
        $this->showSuccessAlert = true; // Activer l'alerte de succès
    }
    // Ajouter cette méthode pour la confirmation
    public function confirmSaveWithDepassement()
    {
        try {
            $this->validate();

            $transaction = new Transaction($this->prepareTransactionData());
            $titre = Titre::find($this->titre_id);

            $transaction->save();
            $titre->update(['VolumeRestant' => -$this->depassementValue]);

            $this->closeDepassementModal();
            $this->resetForm();
            $this->showSuccessAlert = true;

            // Rafraîchir le composant
            $this->dispatch('refreshComponent');

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
        $this->dispatch('showDepassementModal');
    }

    private function calculateVolumesRestants()
    {
        $titre = Titre::find($this->titre_id);
        if (!$titre) return;

        $volumeRestantActuel = $this->getVolumeRestant($titre);

        if ($titre->forme_id == 1) { // Grume
            $this->volumeRestantGrume = $volumeRestantActuel;
            $this->volumeRestantDebite = $this->convertirGrumeEnDebite($volumeRestantActuel);
        } else { // Débité
            $this->volumeRestantDebite = $volumeRestantActuel;
            $this->volumeRestantGrume = $this->convertirDebiteEnGrume($volumeRestantActuel);
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
            'titres' =>  $this->essence_id ? Titre::where('essence_id', $this->essence_id)
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





