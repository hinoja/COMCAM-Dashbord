<?php

namespace App\Livewire\Admin;

use App\Models\Essence;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class EssenceList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Variables pour le formulaire
    public $essence_id;
    public $code;
    public $nom_local;

    // Variable pour le mode édition
    public $isEditing = false;

    protected function rules()
    {
        return [
            'code' => ['required', 'string', 'max:50',
                Rule::unique('essences', 'code')->ignore($this->essence_id)],
            'nom_local' => ['required', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'code.required' => 'Le code est obligatoire',
        'code.unique' => 'Ce code existe déjà',
        'nom_local.required' => 'Le nom local est obligatoire',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        Essence::create([
            'code' => strtoupper($this->code),
            'nom_local' => ucfirst($this->nom_local),
        ]);

        $this->reset(['code', 'nom_local']);
        session()->flash('success', 'Essence ajoutée avec succès');
    }

    public function edit($id)
    {
        $essence = Essence::findOrFail($id);
        $this->essence_id = $essence->id;
        $this->code = $essence->code;
        $this->nom_local = $essence->nom_local;
        $this->isEditing = true;
    }

    public function update()
    {
        $this->validate();

        $essence = Essence::findOrFail($this->essence_id);
        $essence->update([
            'code' => strtoupper($this->code),
            'nom_local' => ucfirst($this->nom_local),
        ]);

        $this->reset(['essence_id', 'code', 'nom_local', 'isEditing']);
        session()->flash('success', 'Essence mise à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        Essence::findOrFail($id)->delete();
        session()->flash('success', 'Essence supprimée avec succès');
    }

    public function cancel()
    {
        $this->reset(['essence_id', 'code', 'nom_local', 'isEditing']);
        $this->resetValidation();
    }

    public function render()
    {
        $essences = Essence::query()
            ->when($this->search, function($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('nom_local', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nom_local')
            ->paginate($this->perPage);

        return view('livewire.admin.essence-list', compact('essences'));
    }
}
