<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\FormeEssence;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EditTitre extends Component
{ 
    public $titreId;
    public $exercice;
    public $nom;
    public $localisation;
    public $zone_id;
    public $details = []; // Tableau de détails
    public $hasTransactions = false;
    public $totalTransactionVolume = 0;
    
    protected $listeners = ['confirmNavigation', 'updateTypeOptions'];
    
    protected $rules = [
        'exercice' => 'required|integer',
        'nom' => 'required|string|max:255',
        'localisation' => 'required|string|max:255',
        'zone_id' => 'required|exists:zones,id|int',
        'details.*.essence_id' => 'required|exists:essences,id',
        'details.*.forme_id' => 'required|exists:formes,id',
        'details.*.type_id' => 'required|exists:types,id',
        'details.*.volume' => 'required|numeric|min:0',
    ];

    public function mount($id)
    {
        $titre = Titre::with([
            'zone', 
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
                    $query->with(['forme', 'type']);
                }]);
            }, 
            'transactions'
        ])->findOrFail($id);

        // Initialiser les données du titre
        $this->titreId = $titre->id;
        $this->exercice = $titre->exercice;
        $this->nom = $titre->nom;
        $this->localisation = $titre->localisation;
        $this->zone_id = $titre->zone_id;
        
        // Initialiser les détails à partir des essences associées
        foreach ($titre->essence as $essence) {
            $formeId = null;
            $typeId = null;
            
            if ($essence->formeEssence) {
                $formeId = $essence->formeEssence->forme_id;
                $typeId = $essence->formeEssence->type_id;
            }
            
            $this->details[] = [
                'essence_id' => $essence->id,
                'forme_id' => $formeId,
                'type_id' => $typeId,
                'volume' => $essence->pivot->volume,
                'volumeRestant' => $essence->pivot->VolumeRestant,
                'original_essence_id' => $essence->id // Pour suivre les essences originales
            ];
        }

        // Vérifier s'il y a des transactions
        $this->hasTransactions = $titre->transactions()->exists();
        $this->totalTransactionVolume = $titre->transactions()->sum('volume');
    }
    
    public function updatedDetails($value, $path)
    {
        $parts = explode('.', $path);
        if ($parts[1] === 'forme_id') {
            $index = $parts[0];
            $this->resetType($index); // Appeler la méthode resetType
        }
        $this->dispatch('loading-start');

        $parts = explode('.', $path);
        if ($parts[1] === 'forme_id') {
            $index = $parts[0];
            if ($value == 1) { // Si Grume est sélectionné
                $this->details[$index]['type_id'] = 1;
            }
        }

        $this->dispatch('loading-end');
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
        return isset($this->details[$index]['forme_id']) && $this->details[$index]['forme_id'] == 2; // ID 2 = Débité
    }
    
    private function isGrumeForm($index)
    {
        return isset($this->details[$index]['forme_id']) && $this->details[$index]['forme_id'] == 1; // ID 1 = Grume
    }
    
    // Réinitialiser le type quand la forme change
    public function resetType($index)
    {
        if ($this->details[$index]['forme_id'] == 1) {
            $this->details[$index]['type_id'] = 1; // Définir automatiquement à 1 pour Grume
        } else {
            $this->details[$index]['type_id'] = '';
        }
    }
    
    public function addDetail()
    {
        if (!$this->hasTransactions) {
            $this->details[] = ['essence_id' => '', 'forme_id' => '', 'type_id' => '', 'volume' => 0, 'volumeRestant' => 0];
        }
    }
    
    public function removeDetail($index)
    {
        if (!$this->hasTransactions) {
            // Vérifier si c'est une essence originale avec des transactions
            if (isset($this->details[$index]['original_essence_id'])) {
                $essenceId = $this->details[$index]['original_essence_id'];
                $titre = Titre::find($this->titreId);
                
                // Vérifier si cette essence a des transactions
                $hasTransactionsForEssence = $titre->transactions()
                    ->where('essence_id', $essenceId)
                    ->exists();
                
                if ($hasTransactionsForEssence) {
                    session()->flash('error', 'Impossible de supprimer cette essence car elle a des transactions associées.');
                    return;
                }
            }
            
            // Modification en profondeur du tableau pour éviter la réactivité immédiate
            $this->details = collect($this->details)
                ->filter(fn($item, $key) => $key !== $index)
                ->values()
                ->toArray();
        }
    }

    public function update()
    {
        try {
            // Ajouter cette validation avant la validation principale
            foreach ($this->details as $index => $detail) {
                if ($this->isDebiteForm($index) && !in_array($detail['type_id'], [2, 3, 4, 5])) {
                    $this->addError("details.$index.type_id", 'Type invalide pour la forme Débité');
                    return;
                }
            }
            
            // Ajout d'une validation de cohérence des données
            if (empty($this->details)) {
                session()->flash('error', 'Ajoutez au moins une ressource avant enregistrement !');
                return;
            }

            // Vérification des doublons
            $uniqueCheck = collect($this->details)
                ->duplicates(fn($item) => $item['essence_id'] . $item['forme_id'] . $item['type_id']);

            if ($uniqueCheck->isNotEmpty()) {
                session()->flash('error', 'Des ressources en doublon ont été détectées !');
                return;
            }
            
            // Validation générale
            $validatedData = $this->validate();
            
            DB::beginTransaction();

            $titre = Titre::findOrFail($this->titreId);
            
            // Mise à jour des informations de base du titre
            $titre->update([
                'exercice' => $validatedData['exercice'],
                'nom' => strtoupper($validatedData['nom']),
                'localisation' => strtoupper($validatedData['localisation']),
                'zone_id' => $validatedData['zone_id'],
            ]);
            
            // Récupérer les essences actuelles pour les comparer
            $currentEssences = $titre->essence()->pluck('essences.id')->toArray();
            $newEssences = collect($this->details)->pluck('essence_id')->toArray();
            
            // Essences à supprimer (présentes dans currentEssences mais pas dans newEssences)
            $essencesToDetach = array_diff($currentEssences, $newEssences);
            
            // Vérifier si les essences à supprimer ont des transactions
            foreach ($essencesToDetach as $essenceId) {
                $hasTransactionsForEssence = $titre->transactions()
                    ->where('essence_id', $essenceId)
                    ->exists();
                
                if ($hasTransactionsForEssence) {
                    throw new \Exception("Impossible de supprimer l'essence ID $essenceId car elle a des transactions associées.");
                }
            }
            
            // Détacher les essences qui ne sont plus présentes
            if (!empty($essencesToDetach)) {
                $titre->essence()->detach($essencesToDetach);
            }
            
            // Mettre à jour ou ajouter les essences
            foreach ($validatedData['details'] as $detail) {
                // Créer ou mettre à jour l'entrée dans FormeEssence
                $this->updateFormeEssence($detail['essence_id'], $detail['forme_id'], $detail['type_id']);
                
                // Vérifier si cette essence existe déjà pour ce titre
                $pivotData = [
                    'volume' => $detail['volume'],
                    'VolumeRestant' => $detail['volume'], // Par défaut, initialiser avec le volume total
                ];
                
                // Si l'essence existe déjà, préserver le VolumeRestant
                $existingPivot = $titre->essence()
                    ->where('essences.id', $detail['essence_id'])
                    ->first();
                
                if ($existingPivot) {
                    // Si le titre a des transactions, calculer le nouveau VolumeRestant
                    if ($this->hasTransactions) {
                        $volumeDifference = $detail['volume'] - $existingPivot->pivot->volume;
                        $pivotData['VolumeRestant'] = $existingPivot->pivot->VolumeRestant + $volumeDifference;
                    }
                    
                    // Mettre à jour l'entrée existante
                    $titre->essence()->updateExistingPivot($detail['essence_id'], $pivotData);
                } else {
                    // Ajouter une nouvelle entrée
                    $titre->essence()->attach($detail['essence_id'], $pivotData);
                }
            }

            DB::commit();
            
            session()->flash('success', 'Titre mis à jour avec succès!');
            return redirect()->route('admin.titre.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
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

    public function render()
    {
        return view('livewire.edit-titre', [
            'zones' => Zone::all(['id', 'name']),
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
            'isLocked' => $this->hasTransactions
        ]);
    }
}
