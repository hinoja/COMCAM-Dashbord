<?php

namespace App\Livewire;

use App\Models\Titre;
use App\Models\Transaction;
use Livewire\Component;

class DashboardTitres extends Component
{
    public $year = 2023;

    public function getTotalVolume()
    {
        return Transaction::whereYear('date', $this->year)->sum('volume');
    }

    public function getTotalTitres()
    {
        return Titre::whereIn('id', Transaction::whereYear('date', $this->year)->pluck('titre_id')->unique())->count();
    }

    public function getTotalTransactions()
    {
        return Transaction::whereYear('date', $this->year)->count();
    }

    public function getTotalDestinations()
    {
        return Transaction::whereYear('date', $this->year)->distinct('destination')->count('destination');
    }

    public function getTotalExportateurs()
    {
        return Transaction::whereYear('date', $this->year)->distinct('societe_id')->count('societe_id');
    }

    public function getMonthlyExportVolume()
    {
        return Transaction::selectRaw('MONTH(date) as month, SUM(volume) as total_volume')
            ->whereYear('date', $this->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                $monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                return [$monthNames[$item->month - 1] => $item->total_volume];
            })->toArray();
    }

    public function getTopExportateurs()
    {
        return Transaction::with('societe') // Charger la relation societe
            ->selectRaw('societe_id, SUM(volume) as total_volume')
            ->whereYear('date', $this->year)
            ->groupBy('societe_id')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get()
            ->mapWithKeys(function ($transaction) {
                return [$transaction->societe?->acronym ?? 'Inconnu' => $transaction->total_volume];
            })->toArray();
    }

    public function getTopEssences()
    {
        return Transaction::selectRaw('essence_id, SUM(volume) as total_volume')
            ->whereYear('date', $this->year)
            ->groupBy('essence_id')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get()
            ->mapWithKeys(function ($transaction) {
                return [$transaction->essence->nom_local => $transaction->total_volume];
            })->toArray();
    }

    public function getTopDestinations()
    {
        return Transaction::selectRaw('destination, SUM(volume) as total_volume')
            ->whereYear('date', $this->year)
            ->groupBy('destination')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getConditionnementDistribution()
    {
        return Transaction::selectRaw('conditionnemment_id, COUNT(*) as count')
            ->whereYear('date', $this->year)
            ->groupBy('conditionnemment_id')
            ->get()
            ->mapWithKeys(function ($transaction) {
                $conditionnement = $transaction->conditionnement;
                return [$conditionnement->designation ?? $conditionnement->code => $transaction->count];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard-titres', [
            'totalVolume' => $this->getTotalVolume(),
            'totalTitres' => $this->getTotalTitres(),
            'totalTransactions' => $this->getTotalTransactions(),
            'totalDestinations' => $this->getTotalDestinations(),
            'totalExportateurs' => $this->getTotalExportateurs(),
            'monthlyExportVolume' => $this->getMonthlyExportVolume(),
            'topExportateurs' => $this->getTopExportateurs(),
            'topEssences' => $this->getTopEssences(),
            'topDestinations' => $this->getTopDestinations(),
            'conditionnementDistribution' => $this->getConditionnementDistribution(),
        ]);
    }
}
