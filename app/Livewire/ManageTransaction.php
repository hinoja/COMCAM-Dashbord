<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Essence;
use App\Models\Forme;
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

    public function delete($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        session()->flash('message', 'Transaction supprimée avec succès!');
    }
    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', $id); // Émet l'événement pour la confirmation
    }

    protected $listeners = ['deleteTransaction' => 'delete']; // Écoute l'événement pour la suppression
    // Méthode pour afficher les détails d'une transaction

    public function showDetails($id)
    {
        $this->selectedTransaction = Transaction::with([
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
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
                'essence' => function($query) {
                    $query->with(['formeEssence' => function($query) {
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
