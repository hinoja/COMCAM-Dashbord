<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\DashboardController;
use App\Livewire\DashboardTitres;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocieteController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\EssenceController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// _____________________________ADMIN_______________________________________________________________________

Route::middleware('auth')->group(function () {
    Route::prefix('users')->name('admin.users.')->controller(UsersController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::patch('status/{user}', 'updateStatus')->name('status');
    });

    //Societes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('societe')->name('societe.')->controller(SocieteController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/import', 'import')->name('import');
            Route::post('/store', 'store')->name('store');
            Route::get('/export', 'export')->name('export');
        });
    });
    // Route::view('/admin/societes', 'admin.societe.index')->name('admin.societe.index');
    // Route::post('/import/societe', [SocieteController::class, 'import'])->name('import.societe.post');
    // Route::post('/import/societe', [SocieteController::class, 'export'])->name('export.societe.post');

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
        Route::get('/', 'index')->name('.index');
        Route::get('/create', 'create')->name('.create');
        Route::post('/store', 'store')->name('.store');
        Route::post('/confirm', 'confirm')->name('.confirm');
        Route::post('/import', 'importTransactions')->name('.import');
        Route::get('/{id}/edit', 'edit')->name('.edit');
        Route::get('/export-by-titre/{titre_id}', 'exportByTitre')->name('.export-by-titre');
        Route::get('/export', 'exportAll')->name('.export'); // Nouvelle route pour l'export général
    });


    Route::post('/import/titres', [TitreController::class, 'import'])->name('import.titre.post');
    Route::view('/dashboard/titres', 'admin.dashboard-titres')->name('dashboard.titres');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/essences', [EssenceController::class, 'index'])->name('essence.index');
    Route::post('/essences', [EssenceController::class, 'store'])->name('essence.store');
    Route::get('/essences/export', [EssenceController::class, 'export'])->name('essence.export');
    Route::post('/essences/import', [EssenceController::class, 'import'])->name('essence.import');
});

// Supprimer ou commenter ce bloc car il crée une duplication
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('admin/essence/export', [EssenceController::class, 'export'])->name('admin.essence.export');
//     Route::post('admin/essence/import', [EssenceController::class, 'import'])->name('admin.essence.import');
// });

require __DIR__ . '/auth.php';


