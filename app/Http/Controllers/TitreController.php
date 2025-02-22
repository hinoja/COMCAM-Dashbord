<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Imports\TitreImport;
use Illuminate\Http\Request;
use App\Http\Requests\TitreRequest;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;

class TitreController extends Controller
{
    // this function will help to list all titles
    public function index()
    {
        $titres = Titre::all();
        // return view('titres.index', compact('titres'));
        return view('admin.titre.index');
    }
    public function edit(Titre $titre) {}

    public function create(Request $request)
    {
        $zones = Zone::query()->get();
        $formes = Forme::query()->get();
        $types = Type::query()->get();
        $essences = Essence::query()->get();

        return view('admin.titre.create', ['zones' => $zones, 'formes' => $formes, 'types' => $types, 'essences' => $essences]);
    }


    // this function will help to add a new title
    public function  addTitre(TitreRequest $request)
    {
        $titre = new Titre([
            'exercice' => $request->exercice,
            'nom' => $request->nom,
            'localisation' => $request->localisation,
            'zone_id' => $request->zone_id,
            'essence_id' => $request->essence_id,
            'forme_id' => $request->forme_id,
            'type_id' => $request->type_id,
            'volume' => $request->volume,
        ]);
        $titre->save();
        notify()->success('Ajout d\'un nouveau Titre avec succès !'); // this is a package for notifications
        return redirect()->back();
    }

    // this function will help to import the list of titles
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        Excel::import(
            new TitreImport,
            $request->file('file')->store('files')
        ); 
        notify()->success('Importation de la liste des titres réussie !'); // this is a package for notifications

        return redirect()->back();
    }
    // this function will help to export the list of titles
    public function export(Request $request)
    {
        return Excel::download(new TitreImport, 'titres.xlsx');
    }
}
