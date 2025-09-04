<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function petugas(Request $request)
    {
        $relPath = 'templates/template_tambah_petugas_excel.xlsx'; // storage/app/templates/...
        if (!Storage::disk('local')->exists($relPath)) {
            return response()->json(['status'=>'error','message'=>'Template tidak ditemukan.'], 404);
        }

        $absPath = Storage::disk('local')->path($relPath);
        return response()->download(
            $absPath,
            'template_tambah_petugas_excel.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function blokSensus(Request $request)
    {
        $relPath = 'templates/template_tambah_blok_sensus_excel.xlsx';
        if (!Storage::disk('local')->exists($relPath)) {
            return response()->json(['status'=>'error','message'=>'Template tidak ditemukan.'], 404);
        }

        $absPath = Storage::disk('local')->path($relPath);
        return response()->download(
            $absPath,
            'template_tambah_blok_sensus_excel.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
