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

    public function resetFilters()
    {
        $this->search = '';
        $this->essenceFilter = '';
        $this->formeFilter = '';
        $this->typeFilter = '';
        $this->perPage = 10;
        $this->resetPage();
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

        return view('livewire.manage-titre', [
            'rows' => $rows,
            'essences' => Essence::query()->get(['id', 'nom_local']),
            'formes' => Forme::query()->get(['id', 'designation']),
            'types' => Type::query()->get(['id', 'code']),
            'searchTerm' => $searchTerm,
        ]);
    }
}
