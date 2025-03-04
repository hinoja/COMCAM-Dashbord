<?php

namespace App\Http\Controllers;

use App\Models\Societe;
use Illuminate\Http\Request;
use App\Imports\SocieteImport;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;

class SocieteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'acronym' => ['required', 'string', 'max:255']
        ]);
        $societe = new Societe([
            'acronym' =>strtoupper($request->acronym)
        ]);
        $societe->save();
        notify()->success('Ajout d\'une nouvelle société avec succès !'); // this is a package for notifications
        return redirect()->back();
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        Excel::import(
            new SocieteImport,
            $request->file('file')->store('files')
        );
        notify()->success('Importation de la liste des sociétés réussie !'); // this is a package for notifications

        return redirect()->back();
    }
    public function exportUsers(Request $request)
    {
        return Excel::download(new SocieteImport, 'users.xlsx');
    }
}
