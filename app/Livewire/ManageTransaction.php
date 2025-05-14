<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Essence;
use App\Models\Forme;
use Illuminate\Support\Facades\DB;
use App\Models\Type;
use App\Models\Societe;
use App\Models\Titre;
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
    public $selectedTransaction = null; // Pour stocker les détails de la transaction sélectionnée



    // public function confirmDelete($id)
    // {
    //     $this->dispatch('confirmDelete', $id); // Émet l'événement pour la confirmation
    // }


    // Correction du listener
    // Supprimer ce listener car il crée une boucle
    // protected $listeners = ['confirmDelete' => 'deleteTransaction'];
    public function deleteTransaction($id)
    {
        try {
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($id); // Utiliser findOrFail au lieu de find

            // Récupérer le titre et l'essence associés
            $titre = $transaction->titre;
            $essence = $transaction->essence;

            if (!$titre || !$essence) {
                throw new \Exception('Les données associées à cette transaction sont incomplètes.');
            }

            // Calculer le volume à restaurer
            $volumeARestaurer = $transaction->volume;

            // Mettre à jour le volume restant dans la table pivot titre_essence
            $titre->essence()->updateExistingPivot($essence->id, [
                'VolumeRestant' => DB::raw("VolumeRestant + $volumeARestaurer")
            ]);

            // Supprimer la transaction
            $transaction->delete();

            DB::commit();
            session()->flash('success', 'Transaction supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression de la transaction : ' . $e->getMessage());
        }
        redirect()->route('admin.transaction.index');
    }

    // Supprimer cette méthode car elle crée une boucle
    // public function confirmDelete($id)
    // {
    //     if ($id) {
    //         $this->deleteTransaction($id);
    //     }
    // }


    public function showDetails($id)
    {
        $this->selectedTransaction = Transaction::with([
            'essence' => function ($query) {
                $query->with(['formeEssence' => function ($query) {
                    $query->with(['forme', 'type']);
                }]);
            },
            'societe',
            'titre'
        ])->findOrFail($id);

        $this->dispatch('showTransactionDetails'); // Émet l'événement pour JS
    }

    // Méthode pour fermer la modale
    public function closeDetails()
    {
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $transactions = Transaction::with([
            'essence' => function ($query) {
                $query->with(['formeEssence' => function ($query) {
                    $query->with(['forme', 'type']);
                }]);
            },
            'societe',
            'titre'
        ])
            ->when($this->search, function ($query) {
                $query->where('destination', 'like', '%' . $this->search . '%')
                    ->orWhere('pays', 'like', '%' . $this->search . '%');
            })
            ->when($this->essenceFilter, function ($query) {
                $query->where('essence_id', $this->essenceFilter);
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
            ->when($this->societeFilter, function ($query) {
                $query->where('societe_id', $this->societeFilter);
            })
            ->when($this->titreFilter, function ($query) {
                $query->where('titre_id', $this->titreFilter);
            })
            ->paginate($this->perPage);

        return view('livewire.manage-transaction', [
            'transactions' => $transactions,
            'essences' => Essence::all(['id', 'nom_local']),
            'formes' => Forme::all(['id', 'designation']),
            'types' => Type::all(['id', 'code']),
            'societes' => Societe::all(['id', 'acronym']),
            'titres' => Titre::all(['id', 'nom']),
        ]);
    }
}
