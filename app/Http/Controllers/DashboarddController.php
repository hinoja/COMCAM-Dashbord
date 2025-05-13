<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use App\Models\Transaction;
use App\Models\Conditionnemment;

class DashboarddController extends Controller
{
    public function loadData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Filtrer les transactions pour l'année spécifiée
        $transactions = Transaction::whereYear('date', $year)->get();

        // Volume total exporté
        $totalVolume = $transactions->sum('volume');

        // Volume mensuel (par mois)
        $monthlyVolumes = $transactions->groupBy(function ($transaction) {
            return $transaction->date->format('F'); // Nom du mois (ex. January)
        })->map->sum('volume')->toArray();

        // Nombre de titres
        $titresCount = Titre::whereIn('id', $transactions->pluck('titre_id')->unique())->count();

        // Nombre d'essences uniques
        $essencesCount = Essence::whereIn('id', $transactions->pluck('essence_id')->unique())->count();

        // Nombre de destinations uniques
        $destinationsCount = $transactions->pluck('destination')->unique()->count();

        // Nombre d'exportateurs (sociétés uniques)
        $exportateursCount = Societe::whereIn('id', $transactions->pluck('societe_id')->unique())->count();

        // Nombre de ports uniques (supposé dans `destination` ou un champ spécifique si disponible)
        $portsCount = $transactions->pluck('destination')->unique()->count(); // À ajuster si un champ `port` existe

        // Nombre de pays uniques
        $paysCount = $transactions->pluck('pays')->unique()->count();

        // Top 10 des essences (par volume)
        $topEssences = $transactions
            ->groupBy('essence_id')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->mapWithKeys(function ($volume, $essenceId) {
                return [Essence::find($essenceId)->nom_local => $volume];
            })->toArray();

        // Top 10 des exportateurs (par volume)
        $topExportateurs = $transactions
            ->groupBy('societe_id')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->mapWithKeys(function ($volume, $societeId) {
                return [Societe::find($societeId)->acronym => $volume];
            })->toArray();

        // Top 10 des destinations (par volume)
        $topDestinations = $transactions
            ->groupBy('destination')
            ->map->sum('volume')
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Répartition des conditionnements (par exemple, conteneur vs conventionnel)
        $conditionnementDistribution = $transactions
            ->groupBy('conditionnemment_id')
            ->map->count()
            ->mapWithKeys(function ($count, $conditionnementId) {
                $conditionnement = Conditionnemment::find($conditionnementId);
                return [$conditionnement->designation ?? $conditionnement->code => $count];
            })->toArray();

        return response()->json([
            'year' => $year,
            'totalVolume' => $totalVolume,
            'monthlyVolumes' => $monthlyVolumes,
            'titresCount' => $titresCount,
            'essencesCount' => $essencesCount,
            'destinationsCount' => $destinationsCount,
            'exportateursCount' => $exportateursCount,
            'portsCount' => $portsCount,
            'paysCount' => $paysCount,
            'topEssences' => $topEssences,
            'topExportateurs' => $topExportateurs,
            'topDestinations' => $topDestinations,
            'conditionnementDistribution' => $conditionnementDistribution
        ]);
    }
}
