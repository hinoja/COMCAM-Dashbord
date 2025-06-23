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

class AddMultipleTransactions extends Component
{
    public $showRemoveConfirmation = false;
    public $transactionIndexToRemove;
    public $showSuccessAlert = false;
    public $showDepassementModal = false;
    public $depassementDetails = [];
    public $currentTransactionIndex = null;
    public $maxTransactions = 10;

    public $exercice;
    public $date = '';

    public $transactions = [];
    public $essences = [];
    public $formes = [];
    public $types = [];
    public $conditionnements = [];
    public $societes = [];
    public $allTitres = [];
    public $allTypes = [];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'confirmRemove' => 'removeTransaction'
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->exercice = date('Y');
        $this->loadSelectData();
        $this->addTransaction();
    }

    private function loadSelectData()
    {
        $this->essences = Essence::orderBy('nom_local')->get(['id', 'nom_local'])->toArray();
        $this->formes = Forme::orderBy('designation')->get(['id', 'designation'])->toArray();
        $this->types = Type::orderBy('code')->get(['id', 'code'])->toArray();
        $this->conditionnements = Conditionnemment::orderBy('code')->get(['id', 'code'])->toArray();
        $this->societes = Societe::orderBy('acronym')->get(['id', 'acronym'])->toArray();
        $this->allTitres = Titre::orderBy('nom')->get(['id', 'nom'])->toArray();
        $this->allTypes = $this->types;
    }

    public function addTransaction()
    {
        if (count($this->transactions) >= $this->maxTransactions) {
            session()->flash('warning', "Vous ne pouvez pas ajouter plus de {$this->maxTransactions} transactions à la fois.");
            return;
        }

        $last = end($this->transactions) ?: [
            'forme_id' => null,
            'type_id' => null,
            'titre_id' => null,
            'essence_id' => null,
            'conditionnemment_id' => 1,
            'societe_id' => 1,
            'pays' => '',
            'destination' => '',
        ];

        $index = count($this->transactions);
        $this->transactions[] = [
            'id' => uniqid(),
            'numero' => $index + 1,
            'forme_id' => $last['forme_id'],
            'type_id' => null,
            'titre_id' => null,
            'essence_id' => $last['essence_id'],
            'conditionnemment_id' => $last['conditionnemment_id'],
            'societe_id' => $last['societe_id'],
            'pays' => $last['pays'],
            'destination' => $last['destination'],
            'volume' => 0,
            'filteredTypes' => $this->allTypes,
            'titres' => $this->allTitres,
            'errors' => [],
            'volumeRestant' => null
        ];

        $this->updateTitresForEssence($index, $this->transactions[$index]['essence_id'] ?? null);
        $this->updateTypesForForme($index, $this->transactions[$index]['forme_id'] ?? null);
    }

    // public function confirmRemoveTransaction($index)
    // {
    //     if (count($this->transactions) <= 1) {
    //         session()->flash('warning', 'Vous devez conserver au moins une transaction.');
    //         return;
    //     }
    //     session()->flash('confirm_remove', ['index' => $index]);
    // }
    public function confirmRemoveTransaction($index)
    {
        if (count($this->transactions) <= 1) return;

        // Stocker uniquement l'index (entier)
        session()->flash('confirm_remove_index', $index);
    }

    // public function removeTransaction($data)
    // {
    //     $index = $data['index'];
    //     unset($this->transactions[$index]);
    //     $this->transactions = array_values($this->transactions);

    //     foreach ($this->transactions as $key => $transaction) {
    //         $this->transactions[$key]['numero'] = $key + 1;
    //     }
    // }
    public function removeTransaction($index)
    {
        unset($this->transactions[$index]);
        $this->transactions = array_values($this->transactions);

        // Re-numéroter les transactions
        foreach ($this->transactions as $key => $transaction) {
            $this->transactions[$key]['numero'] = $key + 1;
        }
    }
    public function duplicateTransaction($index)
    {
        if (count($this->transactions) >= $this->maxTransactions) {
            session()->flash('warning', "Vous ne pouvez pas ajouter plus de {$this->maxTransactions} transactions à la fois.");
            return;
        }

        $transaction = $this->transactions[$index];
        $newTransaction = $transaction;
        $newTransaction['id'] = uniqid();
        $newTransaction['numero'] = count($this->transactions) + 1;
        $newTransaction['volume'] = 0;

        $this->transactions[] = $newTransaction;
        $newIndex = count($this->transactions) - 1;
        $this->updateTitresForEssence($newIndex, $transaction['essence_id']);
        $this->updateTypesForForme($newIndex, $transaction['forme_id']);
    }

    public function updatedTransactions($value, $key)
    {
        $keys = explode('.', $key);
        if (count($keys) < 3) return;

        $index = $keys[1];
        $field = $keys[2];

        // Synchroniser les dépendances immédiatement après chaque changement
        if ($field === 'essence_id') {
            $this->updateTitresForEssence($index, $value);
            $this->transactions[$index]['type_id'] = null;
            $this->transactions[$index]['filteredTypes'] = $this->allTypes;
            $this->updateVolumeRestant($index);
        } elseif ($field === 'forme_id') {
            $this->updateTypesForForme($index, $value);
        } elseif ($field === 'titre_id') {
            $this->updateVolumeRestant($index);
        }

        // Toujours valider après synchronisation
        $this->validateSingleField($index, $field, $value);
    }
    private function validateSingleField($index, $field, $value)
    {
        $rules = $this->rules();
        $fieldKey = "transactions.{$index}.{$field}";

        if (isset($rules[$fieldKey])) {
            try {
                $this->validateOnly($fieldKey);
                $this->transactions[$index]['errors'][$field] = null;
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->transactions[$index]['errors'][$field] = $e->errors()[$fieldKey][0];
            }
        }
    }

    private function updateTitresForEssence($index, $essenceId)
    {
        if ($essenceId) {
            $essence = Essence::with('titres')->find($essenceId);
            $titres = $essence ? $essence->titres->toArray() : [];
        } else {
            $titres = $this->allTitres;
        }
        $this->transactions[$index]['titres'] = $titres;
        $this->transactions[$index]['titre_id'] = null; // Réinitialiser la sélection
        // Réinitialiser le type aussi car il dépend indirectement de l'essence
        $this->transactions[$index]['type_id'] = null;
        $this->transactions[$index]['filteredTypes'] = $this->allTypes;
        // Forcer la réactivité
        $this->transactions = array_values($this->transactions);
    }
    private function updateTypesForForme($index, $formeId)
    {
        if ($formeId) {
            $forme = Forme::with('types')->find($formeId);
            $filteredTypes = $forme ? $forme->types->toArray() : [];
        } else {
            $filteredTypes = $this->allTypes;
        }
        $this->transactions[$index]['filteredTypes'] = $filteredTypes;
        // Réinitialiser le type_id seulement si nécessaire
        if ($formeId == 1) {
            $this->transactions[$index]['type_id'] = 1; // Forcer la sélection
        } else {
            // Si le type sélectionné n'est plus dans la liste filtrée, le réinitialiser
            $currentTypeId = $this->transactions[$index]['type_id'];
            $typeIds = array_column($filteredTypes, 'id');
            if (!in_array($currentTypeId, $typeIds)) {
                $this->transactions[$index]['type_id'] = null;
            }
        }
        // Forcer la réactivité
        $this->transactions = array_values($this->transactions);
    }
    private function updateVolumeRestant($index)
    {
        $transaction = $this->transactions[$index];
        if ($transaction['titre_id'] && $transaction['essence_id']) {
            $titre = Titre::find($transaction['titre_id']);
            if ($titre) {
                $this->transactions[$index]['volumeRestant'] = $this->getVolumeRestant($titre, $transaction['essence_id']);
            } else {
                $this->transactions[$index]['volumeRestant'] = null;
            }
        } else {
            $this->transactions[$index]['volumeRestant'] = null;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'exercice' => ['required', 'int', 'digits:4', 'min:2024'],
        ];
        foreach ($this->transactions as $index => $transaction) {
            $rules["transactions.{$index}.numero"] = ['required', 'numeric', 'min:0'];
            $rules["transactions.{$index}.titre_id"] = ['required', 'int', 'exists:titres,id'];
            $rules["transactions.{$index}.type_id"] = ['required', 'exists:types,id'];
            $rules["transactions.{$index}.essence_id"] = ['required', 'int', 'exists:essences,id'];
            $rules["transactions.{$index}.forme_id"] = ['required', 'int', 'exists:formes,id'];
            $rules["transactions.{$index}.conditionnemment_id"] = ['required', 'int', 'exists:conditionnemments,id'];
            $rules["transactions.{$index}.societe_id"] = ['required', 'int', 'exists:societes,id'];
            $rules["transactions.{$index}.pays"] = ['required', 'string', 'max:255'];
            $rules["transactions.{$index}.destination"] = ['required', 'string', 'max:255'];
            $rules["transactions.{$index}.volume"] = ['required', 'numeric', 'min:0'];
        }
        return $rules;
    }

    public function save()
    {
        try {
            if (empty($this->transactions)) {
                session()->flash('error', 'Ajoutez au moins une transaction avant enregistrement !');
                return;
            }

            $this->validate();

            foreach ($this->transactions as $index => $transaction) {
                if (!empty(array_filter($transaction['errors']))) {
                    session()->flash('error', 'Corrigez les erreurs dans les transactions avant de soumettre.');
                    return;
                }
            }

            $depassements = [];
            $validTransactions = [];

            foreach ($this->transactions as $index => $transactionData) {
                $titre = Titre::find($transactionData['titre_id']);
                if (!$titre) continue;

                $volumeRestant = $this->getVolumeRestant($titre, $transactionData['essence_id']);
                $nouveauVolumeRestant = $volumeRestant - $transactionData['volume'];

                if ($nouveauVolumeRestant < 0) {
                    $depassements[] = [
                        'index' => $index,
                        'depassement' => abs($nouveauVolumeRestant),
                        'transaction' => $transactionData,
                        'titre' => $titre,
                        'volumeRestant' => $volumeRestant
                    ];
                } else {
                    $validTransactions[] = [
                        'index' => $index,
                        'transaction' => $transactionData,
                        'titre' => $titre,
                        'nouveauVolumeRestant' => $nouveauVolumeRestant
                    ];
                }
            }

            if (!empty($depassements)) {
                $this->depassementDetails = $depassements;
                $this->showDepassementModal = true;
                session()->flash('warning', 'Certaines transactions dépassent le volume disponible.');
                $this->dispatch('showDepassementModal');
                return;
            }

            $this->saveAllTransactions($validTransactions);
            session()->flash('success', 'Transactions enregistrées avec succès !');
            $this->dispatch('redirectToList');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Corrigez les erreurs avant de soumettre.');
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'enregistrement : {$e->getMessage()}");
        }
    }

    public function saveValidAndSkipDepassement()
    {
        try {
            $this->validate();

            $validTransactions = [];

            foreach ($this->transactions as $index => $transactionData) {
                $titre = Titre::find($transactionData['titre_id']);
                if (!$titre) continue;

                $volumeRestant = $this->getVolumeRestant($titre, $transactionData['essence_id']);
                $nouveauVolumeRestant = $volumeRestant - $transactionData['volume'];

                if ($nouveauVolumeRestant >= 0) {
                    $validTransactions[] = [
                        'index' => $index,
                        'transaction' => $transactionData,
                        'titre' => $titre,
                        'nouveauVolumeRestant' => $nouveauVolumeRestant
                    ];
                }
            }

            if (!empty($validTransactions)) {
                $this->saveAllTransactions($validTransactions);
                session()->flash('success', 'Transactions valides enregistrées avec succès.');
            } else {
                session()->flash('warning', 'Aucune transaction valide à enregistrer.');
            }

            $this->closeDepassementModal();
            $this->dispatch('redirectToList');
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'enregistrement : {$e->getMessage()}");
        }
    }

    public function confirmSaveWithDepassement()
    {
        try {
            $allTransactions = [];

            foreach ($this->transactions as $index => $transactionData) {
                $titre = Titre::find($transactionData['titre_id']);
                if (!$titre) continue;

                $volumeRestant = $this->getVolumeRestant($titre, $transactionData['essence_id']);
                $nouveauVolumeRestant = $volumeRestant - $transactionData['volume'];

                $allTransactions[] = [
                    'index' => $index,
                    'transaction' => $transactionData,
                    'titre' => $titre,
                    'nouveauVolumeRestant' => $nouveauVolumeRestant
                ];
            }

            $this->saveAllTransactions($allTransactions);
            $this->closeDepassementModal();
            session()->flash('success', 'Transactions enregistrées avec succès, y compris les dépassements.');
            $this->dispatch('redirectToList');
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'enregistrement : {$e->getMessage()}");
        }
    }

    private function saveAllTransactions($transactionsData)
    {
        DB::beginTransaction();
        try {
            foreach ($transactionsData as $data) {
                $transactionData = $data['transaction'];
                $titre = $data['titre'];
                $nouveauVolumeRestant = $data['nouveauVolumeRestant'];

                $transaction = new Transaction([
                    'date' => $this->date,
                    'exercice' => $this->exercice,
                    'numero' => $transactionData['numero'],
                    'societe_id' => $transactionData['societe_id'],
                    'destination' => strtoupper($transactionData['destination']),
                    'pays' => strtoupper($transactionData['pays']),
                    'titre_id' => $transactionData['titre_id'],
                    'essence_id' => $transactionData['essence_id'],
                    'conditionnemment_id' => $transactionData['conditionnemment_id'],
                    'volume' => (float)$transactionData['volume'],
                ]);

                $transaction->save();

                $this->updateFormeEssence(
                    $transactionData['essence_id'],
                    $transactionData['forme_id'],
                    $transactionData['type_id']
                );

                $pivotEntry = $titre->essence()->where('essences.id', $transactionData['essence_id'])->first();

                if ($pivotEntry) {
                    $titre->essence()->updateExistingPivot($transactionData['essence_id'], [
                        'VolumeRestant' => $nouveauVolumeRestant
                    ]);
                }
            }

            DB::commit();
            $this->resetForm();
            $this->showSuccessAlert = true;
            // $this->dispatch('refreshComponent');
            $this->dispatch('redirectToList');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateFormeEssence(int $essenceId, int $formeId, int $typeId): void
    {
        $formeEssence = FormeEssence::firstOrCreate(
            ['essence_id' => $essenceId],
            [
                'forme_id' => $formeId,
                'type_id' => $typeId
            ]
        );

        if ($formeEssence->wasRecentlyCreated) return;

        $formeEssence->update([
            'forme_id' => $formeId,
            'type_id' => $typeId
        ]);
    }

    private function getVolumeRestant(Titre $titre, int $essenceId): float
    {
        $pivotEntry = $titre->essence()->where('essences.id', $essenceId)->first();

        if (!$pivotEntry) {
            return 0.0;
        }

        $hasTransactions = Transaction::where('titre_id', $titre->id)
            ->where('essence_id', $essenceId)
            ->exists();

        return $hasTransactions
            ? (float)($pivotEntry->pivot->VolumeRestant ?: 0.0)
            : (float)($pivotEntry->pivot->volume ?: 0.0);
    }

    public function closeDepassementModal()
    {
        $this->showDepassementModal = false;
        $this->depassementDetails = [];
        $this->dispatch('hideDepassementModal');
    }

    private function resetForm(): void
    {
        $this->transactions = [];
        $this->addTransaction();
        $this->date = now()->format('Y-m-d');
        $this->exercice = date('Y');
    }

    public function reorderTransactions($oldIndex, $newIndex)
    {
        $transaction = array_splice($this->transactions, $oldIndex, 1)[0];
        array_splice($this->transactions, $newIndex, 0, [$transaction]);

        foreach ($this->transactions as $key => $transaction) {
            $this->transactions[$key]['numero'] = $key + 1;
        }
    }

    public function render()
    {
        return view('livewire.add-multiple-transactions');
    }
}
