<?php

namespace App\Livewire\Admin;

use App\Models\Societe;
use Livewire\Component;
use Livewire\WithPagination;

class SocieteList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $societeToDelete = null;

    public function showDeleteForm(Societe $societe)
    {
        $this->societeToDelete = $societe;
        $this->dispatch('openDeleteModal');
    }

    public function delete()
    {
        if ($this->societeToDelete) {
            $this->societeToDelete->delete();
            session()->flash('success', 'Société supprimée avec succès');
            $this->societeToDelete = null;
            $this->dispatch('closeModal');
        }
    }

    public function render()
    {
        return view('livewire.admin.societe-list', [
            'societes' => Societe::query()->orderBy('acronym')->paginate(7)
        ]);
    }
}

