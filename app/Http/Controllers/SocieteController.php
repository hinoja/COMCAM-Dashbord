<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\SocieteImport;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;

class SocieteController extends Controller
{

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        Excel::import(
            new SocieteImport,
            $request->file('file')->store('files')
        );

         toastr()->info('Importation de la liste des sociétés réussie !', 'Succès');
        return redirect()->back();
    }
    public function exportUsers(Request $request)
    {
        return Excel::download(new SocieteImport, 'users.xlsx');
    }
}
