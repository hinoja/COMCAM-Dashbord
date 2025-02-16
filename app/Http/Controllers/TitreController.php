<?php

namespace App\Http\Controllers;

use App\Imports\TitreImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TitreController extends Controller
{
    public function add(){
        return 0;
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        Excel::import(
            new TitreImport,
            $request->file('file')->store('files')
        );
        // $toastr = new Toastr();
        toastr()->success('Importation de la liste des titres réussie !', 'Succès');
        return redirect()->back();
    }
    public function exportUsers(Request $request)
    {
        return Excel::download(new TitreImport, 'titres.xlsx');
    }
}
