<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class AddTitre extends Component
{
    // use LivewireAlert;
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
    public function save()
    {
        // Ajouter cette validation avant la validation principale
        foreach ($this->details as $index => $detail) {
            if ($this->isDebiteForm($index) && !in_array($detail['type_id'], [2, 3, 4, 5])) {
                $this->addError("details.$index.type_id", 'Type invalide pour la forme Débité');
                return;
            }
        }
        // Ajout d'une validation de cohérence des données
        if (empty($this->details)) {
            $this->alert('error', 'Ajoutez au moins une ressource avant enregistrement !');
            return;
        }

        // Vérification des doublons
        $uniqueCheck = collect($this->details)
            ->duplicates(fn($item) => $item['essence_id'] . $item['forme_id'] . $item['type_id']);

        if ($uniqueCheck->isNotEmpty()) {
            $this->alert('error', 'Des ressources en doublon ont été détectées !');
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
            $titres = [];
            foreach ($validatedData['details'] as $detail) {
                $titres[] = [
                    'exercice' => $validatedData['exercice'],
                    'nom' => strtoupper($validatedData['nom']),
                    'localisation' => strtoupper($validatedData['localisation']),
                    'zone_id' => $validatedData['zone_id'],
                    'essence_id' => $detail['essence_id'],
                    'forme_id' => $detail['forme_id'],
                    'type_id' => $detail['type_id'],
                    'volume' => $detail['volume'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Titre::insert($titres);

            DB::commit();
            // LivewireAlert::title('Success')->success()->text('Titre créé avec succès !') ->toast() ->show();

            // $this->alert('success', 'Titre créé avec succès!', [
            //     'position' => 'top-end',
            //     'timer' => 3000,
            //     'toast' => true,
            // ]);
            // toast('Titre créé avec succès!', 'success');
             $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            // LivewireAlert::title('Success')->error()->text("Erreur lors de l'enregistrement : " . $e->getMessage()) ->toast()->show();
            //  $this->alert('error', "Erreur lors de l'enregistrement : " . $e->getMessage());

            // toast("Erreur lors de l'enregistrement : " . $e->getMessage(), 'error');
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
