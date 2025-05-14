<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Conditionnemment;
use App\Imports\TransactionImport;
use App\Http\Requests\TransactionRequest;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Jobs\ExportJob;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with([
            'titre',
            'societe',
            'conditionnemment',
            'essence' => function($query) {
                $query->with(['formeEssence' => function($query) {
                    $query->with(['forme', 'type']);
                }]);
            }
        ])
        ->select('id', 'date', 'exercice', 'numero', 'societe_id', 'destination', 'pays',
                 'titre_id', 'essence_id', 'conditionnemment_id', 'volume', 'created_at')
        ->orderBy('date', 'desc')
        ->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formes = Forme::query()->get(['id', 'designation']);
        $types = Type::query()->get(['id', 'code']);
        $titres = Titre::orderBy('nom')->get(['id', 'nom'])->unique('nom');

        // $titres = Titre::orderBy('nom')->distinct('nom')->get(['id','nom']);//unique
        $essences = Essence::query()->get(['id', 'nom_local']);
        $conditionnements = Conditionnemment::query()->get(['id', 'code']);
        $societes = Societe::query()->get(['id', 'acronym']);

        return view('admin.transactions.create', [
            'types' => $types,
            'formes' => $formes,
            'essences' => $essences,
            'societes' => $societes,
            'titres' => $titres,
            'conditionnements' => $conditionnements
        ]);
    }

    public function importTransactions(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new TransactionImport();
            Excel::import($import, $request->file('file')->store('files'));

            // Récupérer les statistiques d'importation
            $stats = $import->getResultStats();

            $message = $stats['success_count'] . ' transactions importées avec succès.';
            if ($stats['error_count'] > 0) {
                $message .= ' ' . $stats['error_count'] . ' erreurs rencontrées.';
            }

            notify()->success($message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return "Ligne {$failure->row()}: {$failure->errors()[0]}";
            })->join("\n");

            notify()->error('Erreurs de validation dans le fichier: ' . $errors);
        } catch (\Exception $e) {
            notify()->error("Erreur lors de l'importation: " . $e->getMessage());
        }

        return redirect()->back();
    }


    public function confirm(Request $request)
    {
        // Récupérer les données de la transaction depuis la session
        $transactionData = session('transaction_data');

        if (!$transactionData) {
            return redirect()->back()->with('error', 'Aucune donnée de transaction trouvée.');
        }

        // Créer et enregistrer la transaction
        $transaction = new Transaction($transactionData);
        $transaction->save();

        // Supprimer les données de la session
        session()->forget('transaction_data');
        session()->forget('depassement');

        notify()->success('Transaction enregistrée avec succès !');
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('admin.transactions.edit', [
            'transaction' => $transaction
        ]);
    }


    public function exportByTitre($titre_id)
    {
        $titre = Titre::findOrFail($titre_id);

        $query = Transaction::query()
            ->where('titre_id', $titre_id)
            ->with([
                'titre',
                'essence' => function($query) {
                    $query->with(['formeEssence' => function($query) {
                        $query->with(['forme', 'type']);
                    }]);
                },
                'societe',
                'conditionnemment'
            ]);

        $filename = 'transactions_' . Str::slug($titre->nom) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TransactionExport($query), $filename);
    }
    // return Excel::download(new TransactionExport, 'transactionexport_' . date('Y-m-d_H-i-s') . '.xlsx');

    public function exportAll()
    {
        $query = Transaction::query()
            ->with([
                'titre',
                'essence' => function ($query) {
                    $query->with(['formeEssence' => function ($query) {
                        $query->with(['forme', 'type']);
                    }]);
                },
                'societe',
                'conditionnemment'
            ]);

        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TransactionExport($query), $filename);
    }
}





