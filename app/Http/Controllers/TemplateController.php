<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function petugas(Request $request)
    {
        $relPath = 'templates/petugas_import_template.xlsx'; // storage/app/templates/...
        if (!Storage::disk('local')->exists($relPath)) {
            return response()->json(['status'=>'error','message'=>'Template tidak ditemukan.'], 404);
        }

        $absPath = Storage::disk('local')->path($relPath);
        return response()->download(
            $absPath,
            'petugas_import_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function blokSensus(Request $request)
    {
        $relPath = 'templates/blok_sensus_import_template.xlsx';
        if (!Storage::disk('local')->exists($relPath)) {
            return response()->json(['status'=>'error','message'=>'Template tidak ditemukan.'], 404);
        }

        $absPath = Storage::disk('local')->path($relPath);
        return response()->download(
            $absPath,
            'blok_sensus_import_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
