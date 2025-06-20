<?php

namespace App\Livewire\Admin;

use App\Models\Societe;
use Livewire\Component;
use Livewire\WithPagination;

class SocieteList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $perPage = 8;
    public $societe_id;
    public $acronym;
    public $name;
    public $isEditing = false;


    public $societeToDelete = null;
    public function closeModal()
    {
        $this->reset();
        $this->dispatch('closeModal');
    }
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

    public function edit($id)
    {
        $societe = Societe::findOrFail($id);
        $this->societe_id = $societe->id;
        $this->acronym = $societe->acronym;
        $this->name = $societe->name;
        $this->isEditing = true;
    }

    public function update()
    { 
        $this->validate([
            'acronym' => 'required|string|max:255|unique:societes,acronym,' . $this->societe_id, 
        ]);

        $societe = Societe::findOrFail($this->societe_id);
        $societe->update([
            'acronym' => strtoupper($this->acronym),
            'name' => ucfirst($this->name),
        ]);

        $this->reset(['societe_id', 'acronym', 'name', 'isEditing']);
        session()->flash('success', 'Société mise à jour avec succès');
    }
    public function save()
    {
        $this->validate([
                        'acronym' => ['required', 'string', 'max:255', 'unique:societes,acronym']
        ]);
        $societe = new Societe([
            'acronym' => strtoupper($this->acronym)
        ]);
        $societe->save();
        session()->flash('success', 'Ajout d\'une nouvelle société avec succès');
        $this->reset(['acronym']);
        return redirect()->back();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $societes = Societe::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('acronym', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('acronym')
            ->paginate($this->perPage);

        return view('livewire.admin.societe-list', [
            'societes' => $societes
        ]);
    }
}
