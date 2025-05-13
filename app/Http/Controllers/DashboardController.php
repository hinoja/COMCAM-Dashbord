<?php

namespace App\Http\Controllers;

use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use App\Models\Transaction;
use App\Models\Conditionnemment;
use App\Models\Forme;
use App\Models\Type;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'année et le mois sélectionnés (par défaut "all" pour tout)
        $year = $request->input('year', 'all');
        $month = $request->input('month', 'all');

        // Générer les années adjacentes (5 ans avant et après l'année actuelle)
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 2);
        $years = array_merge(['all'], $years);

        // Générer les mois
        $months = [
            'all' => 'Tous les mois',
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril',
            '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', '08' => 'Août',
            '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
        ];

        // Statistiques générales avec filtres
        $stats = $this->getGeneralStats($year, $month);

        // Données pour les graphiques avec filtres
        $chartData = $this->getChartData($year, $month);

        return view('admin.dashboard', [
            'year' => $year,
            'month' => $month,
            'stats' => $stats,
            'chartData' => $chartData,
            'years' => $years,
            'months' => $months,
        ]);
    }

    private function getGeneralStats($year, $month)
    {
        // Requête de base pour les transactions filtrées
        $query = Transaction::query();

        if ($year !== 'all') {
            $query->whereYear('date', $year);
        }
        if ($month !== 'all') {
            $query->whereMonth('date', $month);
        }

        // Statistiques des transactions filtrées
        $totalVolume = $query->sum(DB::raw('COALESCE(volume, 0)'));
        $totalTransactions = $query->count();

        // Statistiques globales (non filtrées)
        $totalTitres = Titre::count();
        $totalEssences = Essence::count();
        $totalSocietes = Societe::count();

        // Nombre total de destinations et pays (sans filtre d'année)
        $totalDestinations = Transaction::whereNotNull('destination')
            ->distinct('destination')
            ->count('destination');

        $totalPays = Transaction::whereNotNull('pays')
            ->distinct('pays')
            ->count('pays');

        // Statistiques des éléments actifs pour la période sélectionnée
        $query = Transaction::query();
        if ($year !== 'all') {
            $query->whereYear('date', $year);
        }
        if ($month !== 'all') {
            $query->whereMonth('date', $month);
        }

        $titresActifs = $query->clone()->distinct('titre_id')->count('titre_id');
        $essencesActives = $query->clone()->distinct('essence_id')->count('essence_id');
        $societesActives = $query->clone()->distinct('societe_id')->count('societe_id');

        return [
            'totalVolume' => $totalVolume,
            'totalTransactions' => $totalTransactions,
            'totalTitres' => $totalTitres,
            'totalEssences' => $totalEssences,
            'totalSocietes' => $totalSocietes,
            'totalDestinations' => $totalDestinations,
            'totalPays' => $totalPays,
            'titresActifs' => $titresActifs,
            'essencesActives' => $essencesActives,
            'societesActives' => $societesActives,
        ];
    }

    private function getChartData($year, $month)
    {
        return [
            'volumeParMois' => $this->getVolumeParMois($year, $month),
            'topEssences' => $this->getTopEssences($year, $month),
            'topSocietes' => $this->getTopSocietes($year, $month),
            'topDestinations' => $this->getTopDestinations($year, $month),
            'volumeParForme' => $this->getVolumeParForme($year, $month),
            'volumeParConditionnement' => $this->getVolumeParConditionnement($year, $month),
            'evolutionAnnuelle' => $this->getEvolutionAnnuelle(),
        ];
    }

    private function getVolumeParMois($year, $month)
    {
        $query = Transaction::selectRaw('MONTH(date) as mois, SUM(volume) as volume_total')
            ->when($year !== 'all', fn($q) => $q->whereYear('date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('date', $month))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->pluck('volume_total', 'mois')
            ->toArray();

        $moisComplet = [];
        for ($i = 1; $i <= 12; $i++) {
            $moisComplet[$i] = $query[$i] ?? 0;
        }

        return [
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'data' => array_values($moisComplet),
        ];
    }

    private function getTopEssences($year, $month)
    {
        $query = Transaction::with('essence')
            ->selectRaw('essence_id, SUM(volume) as volume_total')
            ->when($year !== 'all', fn($q) => $q->whereYear('date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('date', $month))
            ->groupBy('essence_id')
            ->orderByDesc('volume_total')
            ->limit(10)
            ->get();

        return [
            'labels' => $query->map(fn($item) => $item->essence->nom_local)->toArray(),
            'data' => $query->pluck('volume_total')->toArray(),
            'total' => $query->sum('volume_total'),
            'count' => $query->count(),
        ];
    }

    private function getTopSocietes($year, $month)
    {
        $query = Transaction::with('societe')
            ->selectRaw('societe_id, SUM(volume) as volume_total')
            ->when($year !== 'all', fn($q) => $q->whereYear('date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('date', $month))
            ->groupBy('societe_id')
            ->orderByDesc('volume_total')
            ->limit(10)
            ->get();

        return [
            'labels' => $query->map(fn($item) => $item->societe->acronym)->toArray(),
            'data' => $query->pluck('volume_total')->toArray(),
            'total' => $query->sum('volume_total'),
            'count' => $query->count(),
        ];
    }

    private function getTopDestinations($year, $month)
    {
        // Récupérer toutes les destinations avec leur volume total
        $query = Transaction::selectRaw('destination, SUM(volume) as volume_total')
            ->when($year !== 'all', fn($q) => $q->whereYear('date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('date', $month))
            ->whereNotNull('destination')  // Exclure les destinations nulles
            ->groupBy('destination')
            ->orderByDesc('volume_total');

        $destinations = $query->get();

        // Calculer le volume total
        $volumeTotal = $destinations->sum('volume_total');

        // Prendre les 15 premières destinations au lieu de 10
        $topDestinations = $destinations->take(15);

        // Calculer la somme des volumes pour les destinations restantes
        $autresVolume = $volumeTotal - $topDestinations->sum('volume_total');

        // Préparer les données pour le graphique
        $labels = $topDestinations->pluck('destination')->toArray();
        $data = $topDestinations->pluck('volume_total')->toArray();

        // Ajouter la catégorie "Autres" si nécessaire
        if ($autresVolume > 0) {
            $labels[] = 'Autres';
            $data[] = $autresVolume;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => $volumeTotal,
            'count' => $destinations->count(), // Compte total des destinations
        ];
    }

    private function getVolumeParForme($year, $month)
    {
        $query = DB::table('transactions')
            ->join('essences', 'transactions.essence_id', '=', 'essences.id')
            ->join('forme_essences', 'essences.id', '=', 'forme_essences.essence_id')
            ->join('formes', 'forme_essences.forme_id', '=', 'formes.id')
            ->select('formes.designation', DB::raw('COALESCE(SUM(transactions.volume), 0) as volume_total'))
            ->when($year !== 'all', fn($q) => $q->whereYear('transactions.date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('transactions.date', $month))
            ->groupBy('formes.id', 'formes.designation')
            ->orderByDesc('volume_total')
            ->get();

        $volumeTotal = $query->sum('volume_total');
        if ($query->isEmpty()) {
            return ['labels' => ['Aucune donnée'], 'data' => [0], 'total' => 0, 'count' => 0];
        }

        return [
            'labels' => $query->pluck('designation')->toArray(),
            'data' => $query->pluck('volume_total')->map(fn($v) => round($v, 2))->toArray(),
            'total' => round($volumeTotal, 2),
            'count' => $query->count(),
        ];
    }

    private function getVolumeParConditionnement($year, $month)
    {
        $query = DB::table('transactions')
            ->join('conditionnemments', 'transactions.conditionnemment_id', '=', 'conditionnemments.id')
            ->select('conditionnemments.code', 'conditionnemments.designation', DB::raw('SUM(transactions.volume) as volume_total'))
            ->when($year !== 'all', fn($q) => $q->whereYear('transactions.date', $year))
            ->when($month !== 'all', fn($q) => $q->whereMonth('transactions.date', $month))
            ->groupBy('conditionnemments.code', 'conditionnemments.designation')
            ->orderByDesc('volume_total')
            ->get();

        $volumeTotal = $query->sum('volume_total');
        $labels = $query->map(fn($item) => $item->code . ' - ' . $item->designation)->toArray();
        $data = $query->pluck('volume_total')->toArray();

        if (empty($labels)) {
            return ['labels' => ['Aucune donnée'], 'data' => [0], 'total' => 0, 'count' => 0];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => $volumeTotal,
            'count' => count($labels),
        ];
    }

    private function getEvolutionAnnuelle()
    {
        $volumeParAnnee = Transaction::selectRaw('YEAR(date) as annee, SUM(volume) as volume_total')
            ->groupBy('annee')
            ->orderBy('annee')
            ->get()
            ->pluck('volume_total', 'annee')
            ->toArray();

        return [
            'labels' => array_keys($volumeParAnnee),
            'data' => array_values($volumeParAnnee),
        ];
    }
}

