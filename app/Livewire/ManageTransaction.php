<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Essence;
use App\Models\Forme;
use App\Models\Type;
use App\Models\Societe;
use App\Models\Titre;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTransaction extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $essenceFilter = '';
    public $formeFilter = '';
    public $typeFilter = '';
    public $societeFilter = '';
    public $titreFilter = '';
    public $selectedTransaction = null;

    public function resetFilters()
    {
        $this->search = '';
        $this->essenceFilter = '';
        $this->formeFilter = '';
        $this->typeFilter = '';
        $this->societeFilter = '';
        $this->titreFilter = '';
        $this->perPage = 10;
        $this->resetPage();
        return redirect();

    }

    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($id);
            $titre = $transaction->titre;
            $essence = $transaction->essence;

            if (!$titre || !$essence) {
                throw new \Exception('Les données associées à cette transaction sont incomplètes.');
            }

            $volumeARestaurer = $transaction->volume;
            $titre->essence()->updateExistingPivot($essence->id, [
                'VolumeRestant' => DB::raw("VolumeRestant + $volumeARestaurer")
            ]);

            $transaction->delete();

            DB::commit();
            session()->flash('success', 'Transaction supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return redirect()->route('admin.transaction.index');
    }

    public function closeDetails()
    {
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $searchTerm = trim($this->search);

        $transactions = Transaction::with([
            'essence' => function ($query) {
                $query->with(['formeEssence' => function ($query) {
                    $query->with(['forme', 'type']);
                }]);
            },
            'societe',
            'titre'
        ])
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('destination', 'like', '%' . $searchTerm . '%')
                      ->orWhere('pays', 'like', '%' . $searchTerm . '%')
                      ->orWhere('numero', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('titre', function ($q) use ($searchTerm) {
                          $q->where('nom', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('societe', function ($q) use ($searchTerm) {
                          $q->where('acronym', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('essence', function ($q) use ($searchTerm) {
                          $q->where('nom_local', 'like', '%' . $searchTerm . '%');
                      });
                });
            })
            ->when($this->essenceFilter, function ($query) {
                $query->where('essence_id', $this->essenceFilter);
            })
            ->when($this->formeFilter, function ($query) {
                $query->whereHas('essence.formeEssence', function ($q) {
                    $q->where('forme_id', $this->formeFilter);
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->whereHas('essence.formeEssence', function ($q) {
                    $q->where('type_id', $this->typeFilter);
                });
            })
            ->when($this->societeFilter, function ($query) {
                $query->where('societe_id', $this->societeFilter);
            })
            ->when($this->titreFilter, function ($query) {
                $query->where('titre_id', $this->titreFilter);
            })
            ->paginate($this->perPage == 'all' ? Transaction::count() : $this->perPage);

        return view('livewire.manage-transaction', [
            'transactions' => $transactions,
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
            'societes' => Societe::all(['id', 'acronym']),
            'titres' => Titre::all(['id', 'nom']),
            'searchTerm' => $searchTerm, // Passer pour surligner les résultats
        ]);
    }
}
