<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InspectionController;

Route::get('/', fn () => redirect()->route('inspections.create'));

/* ====== FORMULIR MENTAH (PDF kosong, TANPA login) ====== */
Route::get('/inspections/blank',        [InspectionController::class,'blankForm'])->name('inspections.blank');
Route::get('/inspections/blank/export', [InspectionController::class,'blankExport'])->name('inspections.blank.export');

/* ====== AREA TERLINDUNGI (WAJIB LOGIN) ====== */
Route::middleware('auth')->group(function () {

    /* Tanpa Foto (isi & simpan) */
    Route::get('/inspections/create-nophoto',             [InspectionController::class,'createNoPhoto'])->name('inspections.create_nophoto');
    Route::post('/inspections/nophoto',                   [InspectionController::class,'storeNoPhoto'])->name('inspections.store_nophoto');
    Route::get('/inspections/{inspection}/edit-nophoto',  [InspectionController::class,'editNoPhoto'])->name('inspections.edit_nophoto');
    Route::put('/inspections/{inspection}/nophoto',       [InspectionController::class,'updateNoPhoto'])->name('inspections.update_nophoto');

    /* Drafts */
    Route::get('/inspections/drafts',        [InspectionController::class,'drafts'])->name('inspections.drafts');

    /* Dengan Foto */
    Route::get('/inspections/create',        [InspectionController::class,'create'])->name('inspections.create');
    Route::post('/inspections',              [InspectionController::class,'store'])->name('inspections.store');

    Route::get('/inspections/{inspection}/edit',   [InspectionController::class,'edit'])->name('inspections.edit');
    Route::put('/inspections/{inspection}',        [InspectionController::class,'update'])->name('inspections.update');
    Route::delete('/inspections/{inspection}',     [InspectionController::class,'destroy'])->name('inspections.destroy');
    Route::get('/inspections/{inspection}/export', [InspectionController::class,'export'])->name('inspections.export');

    /* Lihat detail hasil kirim */
    Route::get('/inspections/{inspection}',  [InspectionController::class,'show'])->name('inspections.show');
});

/* Rute auth dari Breeze */
require __DIR__.'/auth.php';
