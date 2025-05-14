<?php

namespace App\Livewire;

use App\Models\Titre;
use App\Models\Essence;
use App\Models\Forme;
use App\Models\Type;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTitre extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $essenceFilter = '';
    public $formeFilter = '';
    public $typeFilter = '';
    public $selectedTitre = null; // Pour stocker les détails du titre sélectionné

    public function delete($id)
    {
        try {
            $titre = Titre::findOrFail($id); // Récupérer le titre
            //faire une suppression en cascade
            $titre->transactions()->delete();
            $titre->delete();
            session()->flash('message', 'Titre et toutes les transactions associées supprimée avec succès !');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression du titre.');
        }
        redirect()->route('admin.titre.index');
        // $this->alert('success', 'Titre supprimé avec succès !');

    }
    // Méthode pour afficher les détails d'un titre
    public function showDetails($id)
    {
        $this->selectedTitre = Titre::with([
            'zone',
            'essence' => function ($query) {
                $query->with(['formeEssence' => function ($query) {
                    $query->with(['forme', 'type']);
                }]);
            }
        ])->findOrFail($id);
    }

    // Méthode pour fermer la modale
    public function closeDetails()
    {
        $this->selectedTitre = null;
    }
    public function render()
    {
        // foreach ($essences ){

        // }
        $titres = Titre::with([
            'zone',
            'essence' => function ($query) {
                $query->with(['formeEssence' => function ($query) {
                    $query->with(['forme', 'type']);
                }]);
            }
        ])
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%');
            })
            ->when($this->essenceFilter, function ($query) {
                $query->whereHas('essence', function ($query) {
                    $query->where('essences.id', $this->essenceFilter);
                });
            })
            ->when($this->formeFilter, function ($query) {
                $query->whereHas('essence.formeEssence', function ($query) {
                    $query->where('forme_id', $this->formeFilter);
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->whereHas('essence.formeEssence', function ($query) {
                    $query->where('type_id', $this->typeFilter);
                });
            })
            ->paginate($this->perPage);

        return view('livewire.manage-titre', [
            'titres' => $titres,
            'essences' =>  Essence::query()->get(['id', 'nom_local']),
            'formes' => Forme::query()->get(['id', 'designation']),
            'types' => Type::query()->get(['id', 'code']),
        ]);
    }
}
