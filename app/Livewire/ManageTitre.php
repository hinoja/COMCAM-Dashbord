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

        $titresQuery = Titre::with([
            'zone',
            'essence' => function ($query) {
                if ($this->essenceFilter) {
                    $query->where('essences.id', $this->essenceFilter);
                }
                $query->with(['formeEssence' => function ($subQuery) {
                    if ($this->formeFilter) {
                        $subQuery->where('forme_id', $this->formeFilter);
                    }
                    if ($this->typeFilter) {
                        $subQuery->where('type_id', $this->typeFilter);
                    }
                    $subQuery->with(['forme', 'type']);
                }]);
            }
        ])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nom', 'like', '%' . $searchTerm . '%')
                      ->orWhere('exercice', 'like', '%' . $searchTerm . '%')
                      ->orWhere('localisation', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('zone', function ($q) use ($searchTerm) {
                          $q->where('name', 'like', '%' . $searchTerm . '%');
                      });
                });
            })
            ->when($this->essenceFilter || $this->formeFilter || $this->typeFilter, function ($query) {
                $query->whereHas('essence', function ($q) {
                    if ($this->essenceFilter) {
                        $q->where('essences.id', $this->essenceFilter);
                    }
                    if ($this->formeFilter || $this->typeFilter) {
                        $q->whereHas('formeEssence', function ($subQ) {
                            if ($this->formeFilter) {
                                $subQ->where('forme_id', $this->formeFilter);
                            }
                            if ($this->typeFilter) {
                                $subQ->where('type_id', $this->typeFilter);
                            }
                        });
                    }
                });
            });

        if ($this->perPage === 'all') {
            $titres = $titresQuery->get();
        } else {
            $titres = $titresQuery->paginate((int) $this->perPage);
        }

        return view('livewire.manage-titre', [
            'titres' => $titres,
            'essences' => Essence::query()->get(['id', 'nom_local']),
            'formes' => Forme::query()->get(['id', 'designation']),
            'types' => Type::query()->get(['id', 'code']),
            'searchTerm' => $searchTerm,
        ]);
    }
}
