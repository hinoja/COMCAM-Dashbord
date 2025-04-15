<?php

namespace App\Livewire;

use App\Models\Titre;
use App\Models\Zone;
use App\Models\Essence;
use App\Models\Forme;
use App\Models\Type;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditTitre extends Component
{
    use LivewireAlert;

    public $titreId;
    public $exercice;
    public $nom;
    public $localisation;
    public $zone_id;
    public $essence_id;
    public $forme_id;
    public $type_id;
    public $volume;
    public $volumeInitial;
    public $hasTransactions = false;
    public $totalTransactionVolume = 0;

    protected $rules = [
        'exercice' => 'required|integer',
        'nom' => 'required|string|max:255',
        'localisation' => 'required|string|max:255',
        'zone_id' => 'required|exists:zones,id|int',
        'essence_id' => 'required|exists:essences,id',
        'forme_id' => 'required|exists:formes,id',
        'type_id' => 'required|exists:types,id',
        'volume' => 'required|numeric|min:0',
    ];

    public function mount($id)
    {
        $titre = Titre::with(['zone', 'essence', 'forme', 'type', 'transactions'])
            ->findOrFail($id);

        // Initialiser les données du titre
        $this->titreId = $titre->id;
        $this->exercice = $titre->exercice;
        $this->nom = $titre->nom;
        $this->localisation = $titre->localisation;
        $this->zone_id = $titre->zone_id;
        $this->essence_id = $titre->essence_id;
        $this->forme_id = $titre->forme_id;
        $this->type_id = $titre->type_id;
        $this->volume = $titre->volume;
        $this->volumeInitial = $titre->volume;

        // Vérifier s'il y a des transactions
        $this->hasTransactions = $titre->transactions()->exists();
        $this->totalTransactionVolume = $titre->transactions()->sum('volume');
    }

    public function update()
    {
        try {
            DB::beginTransaction();

            $titre = Titre::with('transactions')->findOrFail($this->titreId);

            // Validation spécifique si le titre a des transactions
            if ($this->hasTransactions) {
                // Empêcher la modification de certains champs critiques
                if ($this->essence_id != $titre->essence_id ||
                    $this->forme_id != $titre->forme_id ||
                    $this->type_id != $titre->type_id) {
                    throw new \Exception("Impossible de modifier l'essence, la forme ou le type car des transactions existent.");
                }

                // Vérifier si le nouveau volume est suffisant pour les transactions existantes
                if ($this->volume < $this->totalTransactionVolume) {
                    throw new \Exception("Le nouveau volume ({$this->volume}) ne peut pas être inférieur au volume total des transactions ({$this->totalTransactionVolume}).");
                }

                // Calculer le nouveau VolumeRestant
                $volumeDifference = $this->volume - $this->volumeInitial;
                $newVolumeRestant = $titre->VolumeRestant + $volumeDifference;
            }

            // Validation générale
            $validatedData = $this->validate();

            // Mise à jour du titre
            $titre->update([
                'exercice' => $validatedData['exercice'],
                'nom' => strtoupper($validatedData['nom']),
                'localisation' => strtoupper($validatedData['localisation']),
                'zone_id' => $validatedData['zone_id'],
                'essence_id' => $validatedData['essence_id'],
                'forme_id' => $validatedData['forme_id'],
                'type_id' => $validatedData['type_id'],
                'volume' => $validatedData['volume'],
                'VolumeRestant' => $this->hasTransactions ? $newVolumeRestant : $validatedData['volume']
            ]);

            DB::commit();

            $this->alert('success', 'Titre mis à jour avec succès!');
            return redirect()->route('admin.titre.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.edit-titre', [
            'zones' => Zone::all(),
            'essences' => $this->hasTransactions ? Essence::where('id', $this->essence_id)->get() : Essence::all(),
            'formes' => $this->hasTransactions ? Forme::where('id', $this->forme_id)->get() : Forme::all(),
            'types' => $this->hasTransactions ? Type::where('id', $this->type_id)->get() : Type::all(),
            'isLocked' => $this->hasTransactions
        ]);
    }
}

