<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Carbon\Carbon;

class StatistiqueDashboard extends Component
{
    public $monthlyChartData;
    public $destinationsChartData;

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
    }

    public function render()
    {
        return view('livewire.statistique-dashboard');
    }
}



