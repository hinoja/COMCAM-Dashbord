<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Conditionnemment;
use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Essence;
use App\Models\Societe;
use App\Models\Titre;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {
        // Créer une nouvelle transaction
        $transaction = new Transaction([
            'date' => $request->date,
            'exercice' => $request->exercice,
            'numero' => $request->numero,
            'societe_id' => $request->societe_id,
            'destination' => strtoupper($request->destination),
            'pays' => strtoupper($request->pays),
            'titre_id' => $request->titre_id,
            'essence_id' => $request->essence_id,
            'forme_id' => $request->forme_id,
            'conditionnemment_id' => $request->conditionnemment_id,
            'type_id' => $request->type_id,
            'volume' => floatval(str_replace(',', '.', $request->volume)),
        ]);

        // Récupérer les informations nécessaires
        $forme_type_transaction = $transaction->forme->designation . '' . $transaction->type->code;
        $forme_type_titre = $transaction->titre->forme->designation . '' . $transaction->titre->type->code;

        //      // Vérifier le dépassement
        // $depassement = $this->calculDepassement($transaction);


        // Logique de calcul du dépassement
        $depassement = 0; // Initialisation du dépassement

        // Cas 1 : Les types de forme et de transaction sont identiques
        if ($forme_type_titre === $forme_type_transaction) {
            $depassement = $transaction->titre->volume - $transaction->volume;
        }
        // Cas 2 à 5 : Conversion de Grume vers d'autres formes
        elseif ($forme_type_titre === 'Grume') {
            switch ($forme_type_transaction) {
                case 'Débité5N': //*2.5
                    $depassement = $transaction->titre->volume - ($transaction->volume / 2.5);
                    break;
                case 'Débité6.1': //
                    $depassement = $transaction->titre->volume - ($transaction->volume / 1.82);
                    break;
                case 'Débité6.2':
                    $depassement = $transaction->titre->volume - ($transaction->volume / 1.67);
                    break;
                case 'PS':
                    $depassement = $transaction->titre->volume - ($transaction->volume / 1.54);
                    break;
            }
        }
        // Cas 6 à 9 : Conversion d'autres formes vers Grume
        elseif ($forme_type_transaction === 'Grume') {
            switch ($forme_type_titre) {
                case 'Débité5N': //coef =0.4
                    $depassement = $transaction->titre->volume - ($transaction->volume * 0.4);
                    break;
                case 'Débité6.1': //*0.8
                    $depassement = $transaction->titre->volume - ($transaction->volume * 0.8);
                    break;
                case 'Débité6.2': //*0.8
                    $depassement = $transaction->titre->volume - ($transaction->volume * 0.8);
                    break;
                case 'PS':
                    $depassement = $transaction->titre->volume - ($transaction->volume * 1.54);
                    break;
            }
        }

        // Si le dépassement est inférieur  à 0, afficher une alerte
        if ($depassement < 0) {
            session()->flash('transaction_data', $transaction->toArray());
            session()->flash('depassement', $depassement);
            return redirect()->back()->with('warning', 'Le dépassement est inférieur ou égal à 0. Souhaitez-vous continuer ?');
        }

        // Enregistrer la transaction si le dépassement est valide
        $transaction->save();
        //chercher le titre correspondant à la transaction
        $titre = Titre::find($transaction->titre_id)->where('essence_id', $transaction->essence_id)->first();
        $titre->VolumeRestant = $depassement;
        $titre->save();
        notify()->success('Transaction enregistrée avec succès !');
        dd(
            "Depassement : " . $depassement,
            "forme et type de la transaction  : " . $forme_type_transaction,
            "Forme et type du titre  : " . $forme_type_titre,
            "Essence du titre : " . $transaction->titre->essence->nom_local,
            "Essence de la transaction  : " . $transaction->essence->nom_local
        );
        return redirect()->back();
    }

    private function calculDepassement($transaction) {}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
