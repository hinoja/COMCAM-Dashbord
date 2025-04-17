<?php

namespace App\Http\Controllers;

use App\Models\Essence;
use Illuminate\Http\Request;
use App\Imports\EssenceImport;
use App\Exports\EssenceExport;
use Maatwebsite\Excel\Facades\Excel;

class EssenceController extends Controller
{
    public function index()
    {
        return view('admin.essence.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_local' => ['required', 'string', 'max:255'],
            'nom_commercial' => ['required', 'string', 'max:255'],
            'nom_scientifique' => ['required', 'string', 'max:255'],
        ]);

        Essence::create([
            'nom_local' => $request->nom_local,
            'nom_commercial' => $request->nom_commercial,
            'nom_scientifique' => $request->nom_scientifique,
        ]);

        notify()->success('Essence ajoutée avec succès !');
        return redirect()->back();
    }

    public function export()
    {
        return Excel::download(new EssenceExport, 'essences_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new EssenceImport, $request->file('file'));
            notify()->success('Importation réussie !');
        } catch (\Exception $e) {
            notify()->error('Erreur lors de l\'importation.');
        }

        return redirect()->back();
    }
}
