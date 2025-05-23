<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Exports\TitreExport;
use App\Imports\TitreImport;
use Illuminate\Http\Request;
use App\Http\Requests\TitreRequest;
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
    // public function edit(Titre $titre) {}

    public function create(Request $request)
    {
        $zones = Zone::query()->get(['id', 'name']);
        $formes = Forme::query()->get(['id', 'designation']);
        $types = Type::query()->get('id', 'code');
        $essences = Essence::query()->get(['id', 'nom_local']);

        return view('admin.titre.create', ['zones' => $zones, 'formes' => $formes, 'types' => $types, 'essences' => $essences]);
    }


    // this function will help to add a new title
    public function  addTitre(TitreRequest $request)
    {

        $titre = new Titre([
            'exercice' => $request->exercice,
            'nom' => strtoupper($request->nom),
            'localisation' => strtoupper($request->localisation),
            'zone_id' => $request->zone_id,
            'essence_id' => $request->essence_id,
            'forme_id' => $request->forme_id,
            'type_id' => $request->type_id,
            // 'volume' =>  floatval(str_replace(',', '.', $request->volume)),
            'volume' =>   $request->volume,
        ]);
        //mettre le texte saisie en majuscule dans la BD
        $titre->save();
        session()->flash('success', 'Ajout d\'un nouveau Titre avec succès !');
        // this is a package for notifications
        return redirect()->back();
    }

    // this function will help to import the list of titles
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            ini_set('max_execution_time', 300);

            $import = new TitreImport();
            Excel::import($import, $request->file('file')->store('files'));

            // Récupérer les statistiques d'importation
            $stats = $import->getResultStats();

            $message = $stats['success_count'] . ' titres importés avec succès.';
            if ($stats['error_count'] > 0) {
                $message .= ' ' . $stats['error_count'] . ' erreurs rencontrées.';
            }

            session()->flash('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return "Ligne {$failure->row()}: {$failure->errors()[0]}";
            })->join("\n");

            session()->flash('error', 'Erreurs de validation dans le fichier: ' . $errors);
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'importation: " . $e->getMessage());
        }

        return redirect()->back();
    }

    public function edit($id)
    {
        $titre = Titre::findOrFail($id);
        // Récupérer les données nécessaires pour le formulaire (zones, essences, formes, types, etc.)
        $zones = Zone::query()->get(['id', 'name']);
        $formes = Forme::query()->get(['id', 'designation']);
        $types = Type::query()->get('id', 'code');
        $essences = Essence::query()->get(['id', 'nom_local']);


        return view('admin.titre.edit', compact('titre', 'zones', 'essences', 'formes', 'types'));
    }
    public function update(Request $request, Titre $titre)
    {
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
            'essence_id' => 'required|exists:essences,id',
            'forme_id' => 'required|exists:formes,id',
            'type_id' => 'required|exists:types,id',
            'volume' => 'required|numeric',
        ]);

        // Mise à jour du titre
        $titre->update($request->all());

        // Redirection avec un message de succès
        return redirect()->route('admin.titre.index')->with('success', 'Titre mis à jour avec succès.');
    }








    public function export()
    {
        return Excel::download(new TitreExport, 'titres_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
