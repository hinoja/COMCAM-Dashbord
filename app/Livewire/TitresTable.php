<?php

namespace App\Livewire;

use App\Models\Titre;
use function Laravel\Prompts\confirm;
use Rappasoft\LaravelLivewireTables\Views\Filter;
use Rappasoft\LaravelLivewireTables\Views\Column;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class TitresTable extends DataTableComponent
{
    protected $model = Titre::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setDefaultSort('created_at', 'desc');
    }

    public function columns(): array
    {
        return [

            Column::make("Exercice", "exercice")
                ->sortable(),
            Column::make("Nom", "nom")
                ->sortable()
                ->searchable(),
            Column::make("Localisation", "localisation")
                ->sortable()
                ->searchable(),
            Column::make("Zone", "zone.name")
                ->sortable(),
            Column::make("Essence", "essence.nom_local")
                ->sortable()
                ->searchable(),
            Column::make("Forme", "forme.designation")
                ->sortable()
                ->searchable(),
            Column::make("Type", "type.code")
                ->sortable()
                ->searchable(),
            Column::make("Volume(m3)", "volume")
                ->sortable()
                ->searchable(),
            // Column::make("cree le", "created_at")->format(fn($value) => $value->format('d-m-Y H:i'))
            //     ->sortable()
            //     ->searchable(),

            ButtonGroupColumn::make("Actions")->buttons([


                LinkColumn::make('Delete')
                    ->title(fn($row) => 'Supprimer')
                    ->location(fn($row) => '#')
                    ->attributes(fn($row) => [
                        'class' => 'btn btn-danger btn-sm',
                        'wire:click' => "deleteTitre({$row->id})",
                        'onclick' => "return confirm('Êtes-vous sûr ?')",
                    ])
            ])



        ];
    }
    public function deleteTitre($id)
    {
        Titre::findOrFail($id)->delete();
        $this->emit('refreshDataTable'); // Rafraîchir la table après suppression
    }

    public function filters(): array
    {
        return [
            TextFilter::make('nom'),
            TextFilter::make('localisation'),
            DateFilter::make('created_at'),
        ];
    }
}
