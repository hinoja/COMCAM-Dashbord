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


class AddTitre extends Component
{

    protected $paginationTheme = 'bootstrap';

    public $exercice;
    public $nom;
    public $localisation;
    public $zone_id;
    public $details = []; // Tableau de détails
    protected $listeners = ['confirmNavigation', 'updateTypeOptions'];
    // Ajouter cette méthode pour gérer le chargement
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
                $this->details[$index]['type_id'] = null;
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
        return $this->details[$index]['forme_id'] == 2; // ID 2 = Débité
    }
    private function isGrumeForm($index)
    {
        return $this->details[$index]['forme_id'] == 1; // ID 1 = Grume
    }

    public function confirmNavigation($navigation)
    {
        if ($this->isDirty()) {
            return $navigation->requireConfirmation(
                'Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter ?'
            );
        }
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

    public function mount()
    {
        $this->initializeForm();
    }

    private function initializeForm()
    {
        $this->exercice = date('Y');
        $this->nom = '';
        $this->localisation = '';
        $this->zone_id = null;
        $this->details = [['essence_id' => '', 'forme_id' => '', 'type_id' => '', 'volume' => 0]];
    }

    public function addDetail()
    {
        $this->details[] = ['essence_id' => '', 'forme_id' => '', 'type_id' => '', 'volume' => 0];
    }


    public function removeDetail($index)
    {
        // Modification en profondeur du tableau pour éviter la réactivité immédiate
        $this->details = collect($this->details)
            ->filter(fn($item, $key) => $key !== $index)
            ->values()
            ->toArray();
    }
    /**
     * Vérifie si une essence existe déjà pour un titre avec le même nom
     *
     * @param string $nom Nom du titre
     * @param int $essenceId ID de l'essence
     * @return array|null Retourne les informations du titre existant ou null
     */
    private function checkExistingEssence($nom, $essenceId)
    {
        // Rechercher un titre avec le même nom
        $existingTitre = Titre::where('nom', strtoupper($nom))
            ->whereHas('essence', function ($query) use ($essenceId) {
                $query->where('essences.id', $essenceId);
            })
            ->with(['essence' => function ($query) use ($essenceId) {
                $query->where('essences.id', $essenceId);
            }])
            ->first();

        if ($existingTitre) {
            $essence = Essence::find($essenceId);
            return [
                'titre' => $existingTitre->nom,
                'essence' => $essence->nom_local,
                'volume' => $existingTitre->essence->first()->pivot->volume,
                'volumeRestant' => $existingTitre->essence->first()->pivot->VolumeRestant,
            ];
        }

        return null;
    }

    public function save()
    {
        // Ajouter cette validation avant la validation principale
        foreach ($this->details as $index => $detail) {
            if ($this->isDebiteForm($index) && !in_array($detail['type_id'], [2, 3, 4, 5])) {
                $this->addError("details.$index.type_id", 'Type invalide pour la forme Débité');
                return;
            }

            // Nous ne faisons plus de vérification ici car elle sera faite lors de l'enregistrement
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
        $validatedData = $this->validate([
            'exercice' => 'required|integer',
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'details.*.essence_id' => 'required|exists:essences,id',
            'details.*.forme_id' => 'required|exists:formes,id',
            // 'details.*.type_id' => 'required|exists:types,id',
            'details.*.type_id' => [
                'required_if:details.*.forme_id,!=,1',
                'exists:types,id',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $formeId = $this->details[$index]['forme_id'];

                    if ($formeId == 1 && $value != 1) {
                        $fail('Le type doit être automatiquement défini pour la forme Grume');
                    }

                    if ($formeId == 2 && !in_array($value, [2, 3, 4, 5])) {
                        $fail('Type invalide pour la forme Débité');
                    }
                }
            ],
            'details.*.volume' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Créer d'abord le titre principal
            $titre = Titre::create([
                'exercice' => $validatedData['exercice'],
                'nom' => strtoupper($validatedData['nom']),
                'localisation' => strtoupper($validatedData['localisation']),
                'zone_id' => $validatedData['zone_id'],
            ]);


            // Ensuite, créer les relations essence_titres pour chaque essence
            $essencesSkipped = []; // Pour stocker les essences ignorées

            foreach ($validatedData['details'] as $detail) {
                // Vérifier si l'essence existe déjà pour un titre avec le même nom
                $existingEssence = $this->checkExistingEssence($titre->nom, $detail['essence_id']);

                if ($existingEssence) {
                    // Ajouter à la liste des essences ignorées
                    $essencesSkipped[] = $existingEssence['essence'];
                    continue; // Passer à l'essence suivante sans l'enregistrer
                }

                // Mettre à jour ou créer l'entrée dans FormeEssence
                $this->updateFormeEssence($detail['essence_id'], $detail['forme_id'], $detail['type_id']);

                // Créer l'entrée dans la table pivot
                $titre->essence()->attach($detail['essence_id'], [
                    'volume' => $detail['volume'],
                    'VolumeRestant' => $detail['volume'], // Initialiser le volume restant
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Vérifier si toutes les essences ont été ignorées
            $allEssencesSkipped = count($essencesSkipped) === count($validatedData['details']);

            if ($allEssencesSkipped) {
                // Si toutes les essences ont été ignorées, supprimer le titre créé
                $titre->delete();
                DB::commit();

                $essencesList = implode(', ', $essencesSkipped);
                session()->flash('error', "Aucune essence n'a été enregistrée car ce titre est déjà associé aux essences suivantes : $essencesList");
                return;
            }

            // Si seulement certaines essences ont été ignorées, afficher un message d'avertissement
            if (!empty($essencesSkipped)) {
                $essencesList = implode(', ', $essencesSkipped);
                session()->flash('warning', "Les essences suivantes n'ont pas été enregistrées car ce titre est déjà associé à ces essences : $essencesList");
            }

            DB::commit();
            $this->resetForm();
            session()->flash('success', __('Titre créé avec succès!!'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __("Erreur lors de l\'enregistrement : " . $e->getMessage()));
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

    private function resetForm()
    {
        $this->initializeForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.add-titre', [
            'zones' => Zone::all(['id', 'name']),
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
        ]);
    }
}
