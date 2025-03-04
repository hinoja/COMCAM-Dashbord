<?php

namespace App\Livewire;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddTitre extends Component
{
    use LivewireAlert;
    public $exercice;
    public $nom;
    public $localisation;
    public $zone_id;
    public $details = [];

    public function mount()
    {
        $this->exercice = date('Y');
        $this->details[] = ['essence_id' => '', 'forme_id' => '', 'type_id' => '', 'volume' => 0];
    }

    public function addDetail()
    {
        $this->details[] = ['essence_id' => '', 'forme_id' => '', 'type_id' => '', 'volume' => 0];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // Re-index the array
    }

    public function save()
    {
        // Validation des données du formulaire
        $data = $this->validate([
            'exercice' => 'required|integer',
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id|int',
            'details.*.essence_id' => 'required|exists:essences,id',
            'details.*.forme_id' => 'required|exists:formes,id',
            'details.*.type_id' => 'required|exists:types,id',
            'details.*.volume' => 'required|numeric|min:0',
        ]);

        // Boucle sur chaque détail pour créer une ligne par essence
        foreach ($data['details'] as $detail) {
            $titre = new Titre([
                'exercice' => $data['exercice'],
                'nom' => strtoupper($data['nom']),
                'localisation' => strtoupper($data['localisation']),
                'zone_id' => $data['zone_id'],
                'essence_id' => $detail['essence_id'],
                'forme_id' => $detail['forme_id'],
                'type_id' => $detail['type_id'],
                 'volume' =>  $detail['volume'],
            ]);

            // Sauvegarde chaque enregistrement
            $titre->save();
        }
        $this->alert('success', 'Ajout d\'un nouveau Titre avec succès !');
        // this is a package for notifications

        $this->reset(['details']);
        $this->mount(); // Reset du formulaire après soumission réussie
    }
    public function render()
    {
        return view('livewire.add-titre', [
            'zones' => Zone::query()->get(['id', 'nom_local']),
            'essences' =>  Essence::query()->get(['id', 'nom_local']),
            'formes' =>Forme::query()->get(['id', 'designation']),
            'types' => Type::query()->get(['id', 'code']),
        ]);
    }

}
