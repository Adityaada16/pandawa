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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/petugas/import', [PetugasController::class, 'import']);

    Route::apiResource('petugas', PetugasController::class)
         ->parameters(['petugas' => 'petugas']); 
    
    Route::apiResource('survei', SurveiController::class)
         ->parameters(['survei' => 'survei']);

    Route::post('/blok_sensus/import', [BlokSensusController::class, 'import']);

    Route::apiResource('blok_sensus', BlokSensusController::class)
         ->parameters(['blok_sensus' => 'blok_sensus']);

    Route::apiResource('rumah_tangga', RumahTanggaController::class)
         ->parameters(['rumah_tangga' => 'rumah_tangga']);

    Route::prefix('templates')->group(function () {
        Route::get('petugas',     [TemplateController::class, 'petugas']);
        Route::get('blok_sensus', [TemplateController::class, 'blokSensus']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

