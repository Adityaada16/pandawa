<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlokSensusController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RumahTanggaController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterSurveiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// -------- Public (tanpa token) --------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// -------- Protected (butuh token Sanctum) --------
Route::middleware(['auth:sanctum'])->group(function () {
    // Info user & logout → semua role
    Route::get('/user', fn (Request $r) => $r->user())->middleware('role:1,2,3,4,5');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('role:1,2,3,4,5');

    // ===== MASTER SURVEI =====
    // READ
    Route::apiResource('master_survei', MasterSurveiController::class)
    ->only(['index','show'])
    ->middleware('role:1,2,3,4,5')
    ->parameters(['master_survei' => 'master_survei']);

    // WRITE
    Route::apiResource('master_survei', MasterSurveiController::class)
    ->except(['index','show'])
    ->middleware('role:1') // admin_prov saja
    ->parameters(['master_survei' => 'master_survei']);

    // ===== SURVEI =====
    // READ
    Route::apiResource('survei', SurveiController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3,4,5')
        ->parameters(['survei' => 'survei']);

    // WRITE
    Route::apiResource('survei', SurveiController::class)
        ->except(['index','show'])
        ->middleware('role:1') // admin_prov saja
        ->parameters(['survei' => 'survei']);

    // ===== PETUGAS =====
    // READ
    Route::apiResource('petugas', PetugasController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3,4,5')
        ->parameters(['petugas' => 'petugas']);

    // WRITE
    Route::apiResource('petugas', PetugasController::class)
        ->except(['index','show'])
        ->middleware('role:1,2')
        ->parameters(['petugas' => 'petugas']);

    // IMPORT
    Route::post('/petugas/import', [PetugasController::class, 'import'])
        ->middleware('role:1,2');

    // ===== BLOK SENSUS =====
    // READ
    Route::apiResource('blok_sensus', BlokSensusController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3,4,5')
        ->parameters(['blok_sensus' => 'blok_sensus']);

    // WRITE
    Route::apiResource('blok_sensus', BlokSensusController::class)
        ->except(['index','show'])
        ->middleware('role:1,2')
        ->parameters(['blok_sensus' => 'blok_sensus']);

    // IMPORT
    Route::post('/blok_sensus/import', [BlokSensusController::class, 'import'])
        ->middleware('role:1,2');

    // ===== RUMAH TANGGA =====
    // READ
    Route::apiResource('rumah_tangga', RumahTanggaController::class)
        ->middleware('role:1,2,3,4,5')
        ->parameters(['rumah_tangga' => 'rumah_tangga']);

    // ===== TEMPLATES (GET only) =====
    Route::prefix('templates')->middleware('role:1,2,3,4,5')->group(function () {
        Route::get('petugas',     [TemplateController::class, 'petugas']);
        Route::get('blok_sensus', [TemplateController::class, 'blokSensus']);
    });

    // ===== PERTANYAAN =====
    // READ → semua role
    Route::apiResource('pertanyaan', PertanyaanController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3,4,5')
        ->parameters(['pertanyaan' => 'pertanyaan']);

    // WRITE → admin & pengolahan
    Route::apiResource('pertanyaan', PertanyaanController::class)
        ->except(['index','show'])
        ->middleware('role:1,2,3')
        ->parameters(['pertanyaan' => 'pertanyaan']);

    // ===== LAPORAN =====
    // READ → semua role (list & detail)
    Route::apiResource('laporan', LaporanController::class)
    ->only(['index','show'])
    ->middleware('role:1,2,3,4,5')
    ->parameters(['laporan' => 'laporan']);

    // WRITE → role lapangan/pengolahan (3,4,5)
    Route::apiResource('laporan', LaporanController::class)
    ->except(['index','show'])
    ->middleware('role:3,4,5')
    ->parameters(['laporan' => 'laporan']);

    // Batch/Helper → role 3,4,5
    Route::prefix('laporan')->middleware('role:3,4,5')->group(function () {
    Route::post('/save',        [LaporanController::class, 'storeBatch']);   // create-or-update batch
    Route::put('/save',         [LaporanController::class, 'updateBatch']);  // strict update batch
    Route::post('/prepopulate', [LaporanController::class, 'prepopulate']);  // buat baris kosong utk semua pertanyaan survei
    Route::get('/unanswered',   [LaporanController::class, 'unanswered']);   // ?id_rumah_tangga=&id_survei=
    });

});