<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Carbon\Carbon;

class StatistiqueDashboard extends Component
{
    public $monthlyChartData;
    public $destinationsChartData;
    public $essencesChartData;
    public $exportateursChartData;
    public $conditionnementChartData;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $transactions = Transaction::whereYear('date', now()->year)->get();

        // Données mensuelles
        $monthlyData = collect(range(1, 12))->map(function($month) use ($transactions) {
            return [
                'x' => Carbon::create(null, $month, 1)->format('F'),
                'y' => $transactions->filter(function($transaction) use ($month) {
                    return Carbon::parse($transaction->date)->month === $month;
                })->sum('volume')
            ];
        })->values()->toArray();

        $this->monthlyChartData = [
            'series' => [[
                'name' => 'Volume',
                'data' => array_column($monthlyData, 'y')
            ]],
            'xaxis' => [
                'categories' => array_column($monthlyData, 'x')
            ]
        ];

        // Données des destinations
        $destinationsData = $transactions->groupBy('destination')
            ->map(function($group) {
                return $group->sum('volume');
            })
            ->sortDesc()
            ->take(10);

        $this->destinationsChartData = [
            'series' => [[
                'name' => 'Volume',
                'data' => $destinationsData->values()->toArray()
            ]],
            'xaxis' => [
                'categories' => $destinationsData->keys()->toArray()
            ]
        ];

        // Données des essences
        $essencesData = $transactions->groupBy('essence_id')
            ->map(function($group) {
                return $group->sum('volume');
            })
            ->sortDesc()
            ->take(10);

        $essenceNames = [];
        foreach ($essencesData as $essenceId => $volume) {
            $essence = $transactions->where('essence_id', $essenceId)->first()->essence ?? null;
            $essenceNames[$essenceId] = $essence ? $essence->nom_local : 'Inconnu';
        }

        $this->essencesChartData = [
            'series' => [[
                'name' => 'Volume',
                'data' => $essencesData->values()->toArray()
            ]],
            'xaxis' => [
                'categories' => collect($essenceNames)->values()->toArray()
            ]
        ];

        // Données des exportateurs
        $exportateursData = $transactions->groupBy('societe_id')
            ->map(function($group) {
                return $group->sum('volume');
            })
            ->sortDesc()
            ->take(10);

        $exportateurNames = [];
        foreach ($exportateursData as $societeId => $volume) {
            $societe = $transactions->where('societe_id', $societeId)->first()->societe ?? null;
            $exportateurNames[$societeId] = $societe ? ($societe->acronym ?? $societe->nom) : 'Inconnu';
        }

        $this->exportateursChartData = [
            'series' => [[
                'name' => 'Volume',
                'data' => $exportateursData->values()->toArray()
            ]],
            'xaxis' => [
                'categories' => collect($exportateurNames)->values()->toArray()
            ]
        ];

        // Données des conditionnements
        $conditionnementData = $transactions->groupBy('conditionnemment_id')
            ->map(function($group) {
                return $group->count();
            })
            ->sortDesc();

        $conditionnementNames = [];
        foreach ($conditionnementData as $conditionnementId => $count) {
            $conditionnement = $transactions->where('conditionnemment_id', $conditionnementId)->first()->conditionnemment ?? null;
            $conditionnementNames[$conditionnementId] = $conditionnement ? ($conditionnement->designation ?? $conditionnement->code) : 'Inconnu';
        }

        $this->conditionnementChartData = [
            'series' => $conditionnementData->values()->toArray(),
            'labels' => collect($conditionnementNames)->values()->toArray()
        ];
    }

    public function render()
    {
        return view('livewire.statistique-dashboard');
    }
}
