<?php

namespace App\Http\Controllers;

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


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(
            new TransactionImport,
            $request->file('file')->store('files')
        );

        return redirect()->back()->with('success', 'Importation des transactions réussie !');
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
}
