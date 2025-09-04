<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlokSensusController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RumahTanggaController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// -------- Public (tanpa token) --------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// -------- Protected (butuh token Sanctum) --------
Route::middleware(['auth:sanctum'])->group(function () {
    // Info user & logout → semua role (1,2,3)
    Route::get('/user', fn (Request $r) => $r->user())->middleware('role:1,2,3');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('role:1,2,3');

    // ===== SURVEI =====
    // GET → role 1,2,3
    Route::apiResource('survei', SurveiController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3')
        ->parameters(['survei' => 'survei']);

    // WRITE (store, update, destroy) → role 1
    Route::apiResource('survei', SurveiController::class)
        ->except(['index','show'])
        ->middleware('role:1')
        ->parameters(['survei' => 'survei']);

    // ===== PETUGAS =====
    // GET → role 1,2,3
    Route::apiResource('petugas', PetugasController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3')
        ->parameters(['petugas' => 'petugas']);

    // WRITE → role 1,2
    Route::apiResource('petugas', PetugasController::class)
        ->except(['index','show'])
        ->middleware('role:1,2')
        ->parameters(['petugas' => 'petugas']);

    // IMPORT → role 1,2
    Route::post('/petugas/import', [PetugasController::class, 'import'])
        ->middleware('role:1,2');

    // ===== BLOK SENSUS =====
    // GET → role 1,2,3
    Route::apiResource('blok_sensus', BlokSensusController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3')
        ->parameters(['blok_sensus' => 'blok_sensus']);

    // WRITE → role 1,2
    Route::apiResource('blok_sensus', BlokSensusController::class)
        ->except(['index','show'])
        ->middleware('role:1,2')
        ->parameters(['blok_sensus' => 'blok_sensus']);

    // IMPORT → role 1,2
    Route::post('/blok_sensus/import', [BlokSensusController::class, 'import'])
        ->middleware('role:1,2');

    // ===== RUMAH TANGGA =====
    // GET → role 1,2,3
    Route::apiResource('rumah_tangga', RumahTanggaController::class)
        ->only(['index','show'])
        ->middleware('role:1,2,3')
        ->parameters(['rumah_tangga' => 'rumah_tangga']);

    // WRITE → role 1,2
    Route::apiResource('rumah_tangga', RumahTanggaController::class)
        ->except(['index','show'])
        ->middleware('role:1,2')
        ->parameters(['rumah_tangga' => 'rumah_tangga']);

    // ===== TEMPLATES (GET only) =====
    Route::prefix('templates')->middleware('role:1,2,3')->group(function () {
        Route::get('petugas',     [TemplateController::class, 'petugas']);
        Route::get('blok_sensus', [TemplateController::class, 'blokSensus']);
    });
});