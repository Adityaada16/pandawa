<?php

namespace App\Http\Controllers;

use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SurveiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Survei::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_master_survei' => ['required','integer','exists:master_surveis,id_master_survei'],
            'nama'           => ['required','string','max:255'],
            'tahun'          => ['required','integer'],
            'periode'        => ['nullable','string','max:20'],
            'status'         => ['nullable', Rule::in(['draft','aktif','selesai'])],
            'laporan'        => ['nullable','boolean'],
            'survei_mulai'   => ['nullable','date'],
            'survei_selesai' => ['nullable','date','after_or_equal:survei_mulai'],
        ], [
            'id_master_survei.required' => 'Master survei wajib diisi.',
            'id_master_survei.integer'  => 'Master survei harus berupa angka.',
            'id_master_survei.exists'   => 'Master survei tidak ditemukan.',
            'nama.required'            => 'Nama survei wajib diisi.',
            'nama.max'                 => 'Nama survei maksimal 255 karakter.',
            'tahun.required'           => 'Tahun survei wajib diisi.',
            'tahun.integer'            => 'Tahun survei harus berupa angka.',
            'periode.max'              => 'Periode maksimal 20 karakter.',
            'status.in'                => 'Status tidak valid. Pilih salah satu: draft, aktif, selesai.',
            'laporan.boolean'          => 'Kolom laporan harus bernilai boolean (0/1).',
            'survei_mulai.date'        => 'survei_mulai harus bertipe tanggal yang valid.',
            'survei_selesai.date'      => 'survei_selesai harus bertipe tanggal yang valid.',
            'survei_selesai.after_or_equal' => 'survei_selesai tidak boleh lebih awal dari survei_mulai.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();
            $survei = Survei::create($data);
    
            return response()->json([
                'status' => 'success',
                'survei' => $survei,
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
    public function show(Survei $survei)
    {
        return ['survei' => $survei];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survei $survei)
    {
        // PUT = wajib isi; PATCH = boleh kosong (nullable) — TANPA "sometimes"
        $required = $request->isMethod('put') ? 'required' : 'nullable';

        $validator = Validator::make($request->all(), [
            'id_master_survei' => [$required,'integer','exists:master_surveis,id_master_survei'],
            'nama'           => [$required,'string','max:255'],
            'tahun'          => [$required,'integer'],
            'periode'        => [$required,'string','max:20'],
            'status'         => [$required, Rule::in(['draft','aktif','selesai'])],
            'laporan'        => [$required,'boolean'],
            'survei_mulai'   => [$required,'date'],
            'survei_selesai' => [$required,'date','after_or_equal:survei_mulai'],
        ], [
            'id_master_survei.required' => 'Master survei wajib diisi.',
            'id_master_survei.integer'  => 'Master survei harus berupa angka.',
            'id_master_survei.exists'   => 'Master survei tidak ditemukan.',
            'nama.required'   => 'Nama survei wajib diisi.',
            'nama.max'        => 'Nama survei maksimal 255 karakter.',
            'tahun.required'  => 'Tahun survei wajib diisi.',
            'tahun.integer'   => 'Tahun survei harus berupa angka.',
            'periode.max'     => 'Periode maksimal 20 karakter.',
            'status.in'       => 'Status tidak valid. Pilih salah satu: draft, aktif, selesai.',
            'laporan.boolean' => 'Kolom laporan harus bernilai boolean (0/1).',
            'survei_mulai.date'   => 'Tanggal survei_mulai tidak valid.',
            'survei_selesai.date' => 'Tanggal survei_selesai tidak valid.',
            'survei_selesai.after_or_equal' => 'Tanggal survei_selesai tidak boleh sebelum survei_mulai.',
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
            if ($survei->$key !== $value) {
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
            $survei->update($data);

            return response()->json([
                'status' => 'success',
                'survei' => $survei->fresh(), // ambil nilai terbaru dari DB
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
    public function destroy(Survei $survei)
    {
        $survei->delete();
        return ['message' => 'data survei dihapus'];
    }
}
