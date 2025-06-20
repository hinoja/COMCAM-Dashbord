<?php

namespace App\Livewire;

use App\Models\Titre;
use App\Models\Essence;
use App\Models\Forme;
use App\Models\Type;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ManageTitre extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $essenceFilter = '';
    public $formeFilter = '';
    public $typeFilter = '';
    public $selectedTitre = null;
    public $showCritiques = false;

    public function resetFilters()
    {
        $this->search = '';
        $this->essenceFilter = '';
        $this->formeFilter = '';
        $this->typeFilter = '';
        $this->perPage = 10;
        $this->showCritiques = false; // <-- Ajoute ceci pour revenir à la liste normale
        $this->resetPage();

        // Réinitialiser également les champs du formulaire si présents
        $this->selectedTitre = null;
    }
    /**
     * Récupère les titres sensibles dont le volume est proche ou dans la zone rouge.
     * On considère "zone rouge" comme volume <= $seuilRouge, et "proche" comme volume <= $seuilAlerte.
     *
     * @param float $seuilRouge  Le seuil de la zone rouge (ex: 10)
     * @param float $seuilAlerte Le seuil d'alerte proche de la zone rouge (ex: 20)
     * @return \Illuminate\Support\Collection
     */

   public function getTitresSensibles($seuilRouge = 10, $seuilAlerte = 20)
{
    return DB::table('essence_titre')
        ->join('titres', 'essence_titre.titre_id', '=', 'titres.id')
        ->join('essences', 'essence_titre.essence_id', '=', 'essences.id')
        ->join('formes', 'essence_titre.forme_id', '=', 'formes.id')
        ->join('types', 'essence_titre.type_id', '=', 'types.id')
        ->join('zones', 'titres.zone_id', '=', 'zones.id')
        ->select(
            'essence_titre.*',
            'titres.nom as titre_nom',
            'titres.exercice',
            'titres.localisation',
            'zones.name as zone_nom',
            'essences.nom_local as essence_nom',
            'formes.designation as forme_nom',
            'types.code as type_code'
        )
        ->where(function ($query) use ($seuilRouge, $seuilAlerte) {
            $query->where('essence_titre.VolumeRestant', '<=', $seuilRouge)
                  ->orWhere('essence_titre.VolumeRestant', '<=', $seuilAlerte);
        })
        ->orderBy('essence_titre.VolumeRestant', 'asc')
        ->paginate($this->perPage); // <-- Utilise paginate ici
}
    public function showTitresSensibles()
    {
        $this->showCritiques = true;
        $this->resetPage(); // facultatif si tu utilises la pagination
        // Utilisez un event ou une propriété déclarée pour transmettre les titres sensibles à la vue
        $this->dispatch('showTitresSensibles', $this->getTitresSensibles());
    }
    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $titre = Titre::findOrFail($id);
            $titre->transactions()->delete();
            $titre->essence()->detach();
            $titre->delete();
            DB::commit();
            session()->flash('success', 'Titre et toutes les données associées supprimées avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
        return redirect()->route('admin.titre.index');
    }

    public function showDetails($id)
    {
        $this->selectedTitre = Titre::with(['zone', 'essence.formeEssence.forme', 'essence.formeEssence.type'])->findOrFail($id);
    }

    public function closeDetails()
    {
        $this->selectedTitre = null;
    }

    public function render()
    {
        if ($this->showCritiques) {
            $rows = $this->getTitresSensibles();
            // Si tu veux paginer, utilise ->forPage($this->page, $this->perPage)
        } else {
            $searchTerm = trim($this->search);

            $query = DB::table('essence_titre')
                ->join('titres', 'essence_titre.titre_id', '=', 'titres.id')
                ->join('essences', 'essence_titre.essence_id', '=', 'essences.id')
                ->join('formes', 'essence_titre.forme_id', '=', 'formes.id')
                ->join('types', 'essence_titre.type_id', '=', 'types.id')
                ->join('zones', 'titres.zone_id', '=', 'zones.id')
                ->select(
                    'essence_titre.*',
                    'titres.nom as titre_nom',
                    'titres.exercice',
                    'titres.localisation',
                    'zones.name as zone_nom',
                    'essences.nom_local as essence_nom',
                    'formes.designation as forme_nom',
                    'types.code as type_code'
                );

            if ($this->essenceFilter) {
                $query->where('essence_titre.essence_id', $this->essenceFilter);
            }
            if ($this->formeFilter) {
                $query->where('essence_titre.forme_id', $this->formeFilter);
            }
            if ($this->typeFilter) {
                $query->where('essence_titre.type_id', $this->typeFilter);
            }
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('titres.nom', 'like', "%$searchTerm%")
                        ->orWhere('titres.exercice', 'like', "%$searchTerm%")
                        ->orWhere('titres.localisation', 'like', "%$searchTerm%")
                        ->orWhere('zones.name', 'like', "%$searchTerm%")
                        ->orWhere('essences.nom_local', 'like', "%$searchTerm%");
                });
            }

            $rows = $query->orderBy('titres.exercice', 'desc')->paginate($this->perPage);
        }

        return view('livewire.manage-titre', [
            'rows' => $rows,
            'essences' => Essence::query()->get(['id', 'nom_local']),
            'formes' => Forme::query()->get(['id', 'designation']),
            'types' => Type::query()->get(['id', 'code']),
            'searchTerm' => $this->search,
        ]);
    }
}
