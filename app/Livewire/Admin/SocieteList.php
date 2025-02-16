<?php

namespace App\Livewire\Admin;

use App\Models\Societe;
use Livewire\Component;
use Livewire\WithPagination;

class SocieteList extends Component
{
    use WithPagination;
    // use LivewireAlert;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {

        return view('livewire.admin.societe-list',['societes' => Societe::query()->orderBy('acronym')->paginate(7)]);
    }
}
