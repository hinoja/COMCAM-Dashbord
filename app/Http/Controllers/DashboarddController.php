<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\Conditionnement;
use App\Models\Conditionnemment;

class DashboarddController extends Controller
{
    public function loadData()
    {
        public $year = 2023;
        public $monthlyVolumes = [];
        public $totalVolume = 0;
        public $titresCount = 0;
        public $essencesCount = 0;
        public $destinationsCount = 0;
        public $exportateursCount = 0;
        public $portsCount = 0;
        public $paysCount = 0;
        public $topEssences = [];
        public $topExportateurs = [];
        public $topDestinations = [];
        public $conditionnementDistribution = [];
        
        // Filtrer les transactions pour l'année spécifiée
        $transactions = Transaction::whereYear('date', $this->year)->get();

        // Volume total exporté
        $this->totalVolume = $transactions->sum('volume');

        // Volume mensuel (par mois)
        $this->monthlyVolumes = $transactions->groupBy(function ($transaction) {
            return $transaction->date->format('F'); // Nom du mois (ex. January)
        })->map->sum('volume')->toArray();

        // Nombre de titres
        $this->titresCount = Titre::whereIn('id', $transactions->pluck('titre_id')->unique())->count();

        // Nombre d'essences uniques
        $this->essencesCount = Essence::whereIn('id', $transactions->pluck('essence_id')->unique())->count();

        // Nombre de destinations uniques
        $this->destinationsCount = $transactions->pluck('destination')->unique()->count();

        // Nombre d'exportateurs (sociétés uniques)
        $this->exportateursCount = Societe::whereIn('id', $transactions->pluck('societe_id')->unique())->count();

        // Nombre de ports uniques (supposé dans `destination` ou un champ spécifique si disponible)
        $this->portsCount = $transactions->pluck('destination')->unique()->count(); // À ajuster si un champ `port` existe

        // Nombre de pays uniques
        $this->paysCount = $transactions->pluck('pays')->unique()->count();

        // Top 10 des essences (par volume)
        $this->topEssences = $transactions
            ->groupBy('essence_id')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->mapWithKeys(function ($volume, $essenceId) {
                return [Essence::find($essenceId)->nom_local => $volume];
            })->toArray();

        // Top 10 des exportateurs (par volume)
        $this->topExportateurs = $transactions
            ->groupBy('societe_id')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->mapWithKeys(function ($volume, $societeId) {
                return [Societe::find($societeId)->acronym => $volume];
            })->toArray();

        // Top 10 des destinations (par volume)
        $this->topDestinations = $transactions
            ->groupBy('destination')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Répartition des conditionnements (par exemple, conteneur vs conventionnel)
        $this->conditionnementDistribution = $transactions
            ->groupBy('conditionnemment_id')
            ->map->count()
            ->mapWithKeys(function ($count, $conditionnementId) {
                $conditionnement = Conditionnemment::find($conditionnementId);
                return [$conditionnement->designation ?? $conditionnement->code => $count];
            })->toArray();
    }
}
