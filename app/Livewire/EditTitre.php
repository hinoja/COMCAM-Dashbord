<?php

namespace App\Livewire;

use App\Models\Titre;
use App\Models\Zone;
use App\Models\Essence;
use App\Models\Forme;
use App\Models\Type;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditTitre extends Component
{


    public $titreId;
    public $exercice;
    public $nom;
    public $localisation;
    public $zone_id;
    public $essence_id;
    public $forme_id;
    public $type_id;
    public $volume;

    public function mount($id)
    {
        $titre = Titre::with(['zone', 'essence', 'forme', 'type'])->findOrFail($id);

        $this->titreId = $titre->id;
        $this->exercice = $titre->exercice;
        $this->nom = $titre->nom;
        $this->localisation = $titre->localisation;
        $this->zone_id = $titre->zone_id;
        $this->essence_id = $titre->essence_id;
        $this->forme_id = $titre->forme_id;
        $this->type_id = $titre->type_id;
        $this->volume = $titre->volume;
    }

    public function update()
    {
        $data = $this->validate([
            'exercice' => 'required|integer',
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id|int',
            'essence_id' => 'required|exists:essences,id',
            'forme_id' => 'required|exists:formes,id',
            'type_id' => 'required|exists:types,id',
            'volume' => 'required|numeric|min:0',
        ]);

        $titre = Titre::findOrFail($this->titreId);

        $titre->update([
            'exercice' => $data['exercice'],
            'nom' => strtoupper($data['nom']),
            'localisation' => strtoupper($data['localisation']),
            'zone_id' => $data['zone_id'],
            'essence_id' => $data['essence_id'],
            'forme_id' => $data['forme_id'],
            'type_id' => $data['type_id'],
            'volume' => $data['volume'],
        ]);

        // $this->alert('success', 'Titre mis à jour avec succès !');
        return redirect()->route('admin.titre.index');
    }

    public function render()
    {
        return view('livewire.edit-titre', [
            'zones' => Zone::all(),
            'essences' => Essence::all(),
            'formes' => Forme::all(),
            'types' => Type::all(),
        ]);
    }
}
