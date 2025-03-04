<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocieteController;
use App\Http\Controllers\TitreController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
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
    // Route::post('/{titre}/update', 'update')->name('.update');
});
//Transactions
//Transactions
Route::controller(TransactionController::class)->prefix('transaction')->name('admin.transaction')->group(function () {
    // Route::get('/', 'index')->name('.index');
    // Route::post('{titre}/edit', 'edit')->name('.edit');
    Route::get('/create', 'create')->name('.create');
    Route::post('/store', 'store')->name('.store');
    Route::post('/confirm', 'confirm')->name('.confirm');  // Modified confirm route
    // Route::post('/import', 'import')->name('.import');
    // Route::get('export/',  'export')->name('.export');
});

// Route::view('/admin/titres/ajout', 'admin.titre.index')->name('admin.titre.index');
// Route::view('/admin/titres', 'admin.titre.index');
Route::post('/import/titres', [TitreController::class, 'import'])->name('import.titre.post');

require __DIR__ . '/auth.php';
