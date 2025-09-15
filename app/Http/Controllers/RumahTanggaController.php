<?php

namespace App\Http\Controllers;

use App\Models\RumahTangga;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class RumahTanggaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RumahTangga::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bs' => [
                'required', 'integer',
                Rule::exists('blok_sensuses', 'id_bs'),
            ],
            'nurt' => [
                'required', 'integer',
                // unik komposit: (id_bs, nurt)
                Rule::unique('rumah_tanggas')->where(fn($q) =>
                    $q->where('id_bs', $request->input('id_bs'))
                ),
            ],
            'krt'        => ['required','string','max:50'],
            'keterangan' => ['nullable','string'],
            'waktu_mulai'   => ['nullable','date'],
            'waktu_selesai' => ['nullable','date','after_or_equal:waktu_mulai'],
            'ladt'      => ['nullable','string'],
            'longt'     => ['nullable','string'],
            'ladt_pml'  => ['nullable','string'],
            'longt_pml' => ['nullable','string'],
            'status_proses_pengawasan' => ['nullable','integer'],
            'waktu_pengawasan'         => ['nullable','date'],
            'cacah1' => ['nullable','string','max:5'],
            'cacah2' => ['nullable','string','max:5'],
            'periksa1'  => ['nullable','string','max:5'],
            'periksa2'  => ['nullable','string','max:5'],
            'periksa3'  => ['nullable','string','max:5'],
            'periksa4'  => ['nullable','string','max:5'],
            'periksa5'  => ['nullable','string','max:5'],
            'periksa6'  => ['nullable','string','max:5'],
            'periksa7'  => ['nullable','string','max:5'],
            'periksa8'  => ['nullable','string','max:5'],
            'periksa9'  => ['nullable','string','max:5'],
            'periksa10' => ['nullable','string','max:5'],
            'periksa11' => ['nullable','string','max:5'],
            'periksa12' => ['nullable','string','max:5'],
            'periksa13' => ['nullable','string','max:5'],
            'periksa14' => ['nullable','string','max:5'],
            'periksa15' => ['nullable','string','max:5'],
            'kirim1' => ['nullable','string','max:5'],
            'kirim2' => ['nullable','string','max:5'],
        ], [
            'id_bs.required' => 'Blok Sensus (ID) wajib diisi.',
            'id_bs.integer'  => 'Blok Sensus harus berupa ID angka.',
            'id_bs.exists'   => 'Blok Sensus tidak ditemukan.',
            'id_bs.unique'   => 'Blok Sensus ini sudah memiliki data rumah tangga.',
            'nurt.required'  => 'NURT wajib diisi.',
            'nurt.integer'   => 'NURT harus berupa angka.',
            'krt.required'   => 'Nama KRT wajib diisi.',
            'krt.max'        => 'Nama KRT maksimal 50 karakter.',
            'waktu_mulai.date'      => 'Waktu mulai tidak valid.',
            'waktu_selesai.date'    => 'Waktu selesai tidak valid.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus ≥ waktu mulai.',
            'status_proses_pengawasan.integer' => 'Status pengawasan harus berupa angka.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();
    
            $rumahTangga = RumahTangga::create($data);
    
            return response()->json([
                'status'       => 'success',
                'rumah_tangga' => $rumahTangga,
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
    public function show(RumahTangga $rumahTangga)
    {
        return ['rumah_tangga' => $rumahTangga];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RumahTangga $rumahTangga)
    {
        // Kalau PUT => required, kalau PATCH => sometimes
        $required = $request->isMethod('put') ? 'required' : 'sometimes';
        $validator = Validator::make($request->all(), [
            'id_bs' => [
            $required, 'integer',
            Rule::exists('blok_sensuses', 'id_bs'),
        ],
        'nurt' => [
            $required, 'integer',
            // unik komposit (id_bs, nurt), abaikan baris saat ini dengan PK id_rt
            Rule::unique('rumah_tanggas')->where(fn($q) =>
                $q->where('id_bs', $request->input('id_bs', $rumahTangga->id_bs))
            )->ignore($rumahTangga->id_rt, 'id_rt'),
        ],
            'krt'        => [$required,'string','max:50'],
            'keterangan' => ['nullable','string'],
            'waktu_mulai'   => ['nullable','date'],
            'waktu_selesai' => ['nullable','date','after_or_equal:waktu_mulai'],
            'ladt'      => ['nullable','string'],
            'longt'     => ['nullable','string'],
            'ladt_pml'  => ['nullable','string'],
            'longt_pml' => ['nullable','string'],
            'status_proses_pengawasan' => ['nullable','integer'],
            'waktu_pengawasan'         => ['nullable','date'],
            'cacah1' => ['nullable','string','max:5'],
            'cacah2' => ['nullable','string','max:5'],
            'periksa1'  => ['nullable','string','max:5'],
            'periksa2'  => ['nullable','string','max:5'],
            'periksa3'  => ['nullable','string','max:5'],
            'periksa4'  => ['nullable','string','max:5'],
            'periksa5'  => ['nullable','string','max:5'],
            'periksa6'  => ['nullable','string','max:5'],
            'periksa7'  => ['nullable','string','max:5'],
            'periksa8'  => ['nullable','string','max:5'],
            'periksa9'  => ['nullable','string','max:5'],
            'periksa10' => ['nullable','string','max:5'],
            'periksa11' => ['nullable','string','max:5'],
            'periksa12' => ['nullable','string','max:5'],
            'periksa13' => ['nullable','string','max:5'],
            'periksa14' => ['nullable','string','max:5'],
            'periksa15' => ['nullable','string','max:5'],
            'kirim1' => ['nullable','string','max:5'],
            'kirim2' => ['nullable','string','max:5'],
        ], [
            'id_bs.required' => 'Blok Sensus (ID) wajib diisi.',
            'id_bs.integer'  => 'Blok Sensus harus berupa ID angka.',
            'id_bs.exists'   => 'Blok Sensus tidak ditemukan.',
            'id_bs.unique'   => 'Blok Sensus ini sudah memiliki data rumah tangga.',
            'nurt.required'  => 'NURT wajib diisi.',
            'nurt.integer'   => 'NURT harus berupa angka.',
            'krt.required'   => 'Nama KRT wajib diisi.',
            'krt.max'        => 'Nama KRT maksimal 50 karakter.',
            'waktu_mulai.date'      => 'Waktu mulai tidak valid.',
            'waktu_selesai.date'    => 'Waktu selesai tidak valid.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus ≥ waktu mulai.',
            'status_proses_pengawasan.integer' => 'Status pengawasan harus berupa angka.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        // Cek apakah ada data yang benar-benar diubah
        $data = $validator->validated();
    
        $changes = false;
        foreach ($data as $key => $value) {
            if ($rumahTangga->$key !== $value) {
                $changes = true;
                break;
            }
        }

        // Jika tidak ada perubahan
        if (!$changes) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada perubahan data untuk diupdate.',
            ]   
            , Response::HTTP_BAD_REQUEST);
        }

        try {
            // $data = $validator->validated();
    
            $rumahTangga->update($data);
    
            return response()->json([
                'status'       => 'success',
                'rumah_tangga' => $rumahTangga->fresh(), // nilai terbaru dari DB
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
    public function destroy(RumahTangga $rumahTangga)
    {
        $rumahTangga->delete();
        return ['message' => 'data rumah tangga dihapus'];
    }
}
