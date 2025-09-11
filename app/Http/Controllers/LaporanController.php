<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\RumahTangga;
use App\Models\Survei;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Laporan::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_rumah_tangga' => ['required','integer','exists:rumah_tanggas,id_rt'],
            'id_survei' => [
                'required','integer',
                Rule::exists('surveis','id_survei'),
                // memastikan ada baris di pertanyaans dengan id_survei tsb
                Rule::exists('pertanyaans','id_survei'),
            ],
            'jawaban'         => ['required','array','min:1'],
            'jawaban.*.id_pertanyaan' => [
                'required','integer',
                Rule::exists('pertanyaans','id_pertanyaan')
                    ->where(fn($q) => $q->where('id_survei', $request->input('id_survei'))),
            ],
            'jawaban.*.jawaban' => ['nullable','string'],
        ], [
            'id_rumah_tangga.required' => 'Rumah tangga wajib diisi.',
            'id_rumah_tangga.exists'   => 'Rumah tangga tidak valid.',
            'id_survei.required'       => 'Survei wajib diisi.',
            'id_survei.exists'         => 'Survei tidak valid.',
            'jawaban.required'         => 'Daftar jawaban wajib diisi.',
            'jawaban.array'            => 'Format jawaban tidak valid.',
            'jawaban.*.id_pertanyaan.required' => 'ID pertanyaan wajib diisi.',
            'jawaban.*.id_pertanyaan.exists'   => 'Pertanyaan tidak valid untuk survei ini.',
            'jawaban.*.jawaban.string'         => 'Jawaban harus berupa teks.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $idRt = $request->id_rumah_tangga;
            $idSurvei = $request->id_survei;
            $items = $request->jawaban;
    
            DB::transaction(fn() => Laporan::upsertBatchForRt($idRt, $items));
    
            $survei = Survei::findOrFail($idSurvei);
            $progress = $survei->progressForRt($idRt);
    
            $rt = RumahTangga::findOrFail($idRt);
            $laporans = $rt->laporansForSurvei($idSurvei)
                           ->get(['id_laporan','id_pertanyaan','jawaban']);
    
            return response()->json([
                'status'   => 'success',
                'progress' => $progress,
                'data'     => $laporans,
            ], Response::HTTP_OK);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Laporan $laporan)
    {
        return ['laporan' => $laporan];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $laporan)
    {
        $required = $request->isMethod('put') ? 'required' : 'nullable';

        $validator = Validator::make($request->all(), [
            'id_rumah_tangga' => ['required','integer','exists:rumah_tanggas,id_rt'],
            'id_survei'       => ['required','integer','exists:surveis,id_survei'],
            'jawaban'         => ['required','array','min:1'],
            'jawaban.*.id_pertanyaan' => [
                'required','integer',
                Rule::exists('pertanyaans','id_pertanyaan')
                    ->where(fn($q) => $q->where('id_survei', $request->input('id_survei'))),
            ],
            'jawaban.*.jawaban' => ['nullable','string'],
        ], [
            'id_rumah_tangga.required' => 'Rumah tangga wajib diisi.',
            'id_rumah_tangga.exists'   => 'Rumah tangga tidak valid.',
            'id_survei.required'       => 'Survei wajib diisi.',
            'id_survei.exists'         => 'Survei tidak valid.',
            'jawaban.required'         => 'Daftar jawaban wajib diisi.',
            'jawaban.array'            => 'Format jawaban tidak valid.',
            'jawaban.*.id_pertanyaan.required' => 'ID pertanyaan wajib diisi.',
            'jawaban.*.id_pertanyaan.exists'   => 'Pertanyaan tidak valid untuk survei ini.',
            'jawaban.*.jawaban.string'         => 'Jawaban harus berupa teks.',
        ]);
    
        if ($validator->fails()) {
             return response()->json([
                 'status'  => 'error',
                 'message' => $validator->errors(),
             ], Response::HTTP_BAD_REQUEST);
        }
 
        try {
            $idRt     = (int) $request->id_rumah_tangga;
            $idSurvei = (int) $request->id_survei;
            $items    = $request->jawaban;
    
            // 🔎 Pastikan SEMUA pasangan (RT, pertanyaan) sudah ada (strict update)
            $idsYangDiminta = collect($items)->pluck('id_pertanyaan')->all();
    
            $idsYangAda = Laporan::query()
                ->where('id_rumah_tangga', $idRt)
                ->whereIn('id_pertanyaan', $idsYangDiminta)
                ->pluck('id_pertanyaan')
                ->all();
    
            $missing = array_values(array_diff($idsYangDiminta, $idsYangAda));
            if (!empty($missing)) {
                // Kalau mau menampilkan label pertanyaan yang hilang, bisa join ke tabel pertanyaans dulu
                return response()->json([
                    'status'  => 'error',
                    'message' => [
                        'jawaban' => ['Sebagian pasangan (RT, pertanyaan) belum ada di database. Pre-populate dulu atau gunakan endpoint create.'],
                        'id_pertanyaan_missing' => $missing,
                    ],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            // 🔄 Update hanya baris yang memang sudah ada
            DB::transaction(function () use ($idRt, $items) {
                foreach ($items as $it) {
                    Laporan::where('id_rumah_tangga', $idRt)
                        ->where('id_pertanyaan', $it['id_pertanyaan'])
                        ->update(['jawaban' => $it['jawaban'] ?? null]);
                }
            });
    
            // 📊 Progress & data terkini (pakai helper di Model, sama seperti store)
            $survei   = Survei::findOrFail($idSurvei);
            $progress = $survei->progressForRt($idRt);
    
            $rt       = RumahTangga::findOrFail($idRt);
            $laporans = $rt->laporansForSurvei($idSurvei)
                           ->get(['id_laporan','id_pertanyaan','jawaban']);
    
            return response()->json([
                'status'   => 'success',
                'progress' => $progress,
                'data'     => $laporans,
            ], Response::HTTP_OK);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        $laporan->delete();
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Laporan berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
