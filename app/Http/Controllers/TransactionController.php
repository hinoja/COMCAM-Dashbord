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
    public function store(Request $request)
    { 
        $transaction = new Transaction([
            'date' => $request->date,
            'exercice' => $request->exercice,
            'numero' => $request->numero,
            'societe_id' => $request->societe_id,
            'destination' => $request->destination,
            'pays' => $request->pays,
            'titre_id' => $request->titre_id,
            'essence_id' => $request->essence_id,
            'forme_id' => $request->forme_id,
            'conditionnemment_id' => $request->conditionnemment_id,
            'type_id' => $request->type_id,
            'volume' => $request->volume,
        ]);
        $transaction->save();
        notify()->success('Ajout d\'une nouvelle transaction avec succes !'); // this is a package for notifications

        return redirect()->back();
    }

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
}
