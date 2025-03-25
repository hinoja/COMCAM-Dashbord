<?php

use App\Livewire\DashboardTitres;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocieteController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// _____________________________ADMIN_______________________________________________________________________
Route::middleware('auth')->group(function () {

    Route::view('/admin/societes', 'admin.societe.index')->name('admin.societe.index');
    Route::post('/import/societe', [SocieteController::class, 'import'])->name('import.societe.post');

    //titres
    Route::controller(TitreController::class)->prefix('titre')->name('admin.titre')->group(function () {
        Route::get('/', 'index')->name('.index');
        Route::get('/create', 'create')->name('.create');
        Route::post('/store', 'addTitre')->name('.store');
        Route::post('/import', 'import')->name('.import');
        Route::get('/export',  'export')->name('.export');
        Route::get('/{id}/edit', 'edit')->name('.edit');      // This is the route causing issues
    });
    //Transactions
    Route::controller(TransactionController::class)->prefix('transaction')->name('admin.transaction')->group(function () {
        Route::get('/create', 'create')->name('.create');
        Route::post('/store', 'store')->name('.store');
        Route::post('/confirm', 'confirm')->name('.confirm');  // Modified confirm route
        Route::post('/import', 'importTransactions')->name('.import');
        // Route::get('export/',  'export')->name('.export');
    });


    Route::post('/import/titres', [TitreController::class, 'import'])->name('import.titre.post');
    Route::view('/dashboard/titres', 'admin.dashboard')->name('dashboard.titres');
});

require __DIR__ . '/auth.php';
