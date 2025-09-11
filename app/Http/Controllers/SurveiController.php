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
            'kode'      => ['required','string','max:20', 'unique:surveis,kode'],
            'nama'      => ['required','string','max:255'],
            'deskripsi' => ['nullable','string'],
            'tahun'     => ['required','integer'],
            'periode'   => ['nullable','string','max:20'],
            'status'    => ['nullable', Rule::in(['draft','aktif','selesai'])],
            'laporan'   => ['nullable','boolean'], // 0/1, true/false, "0"/"1"
        ], [
            'kode.required'   => 'Kode survei wajib diisi.',
            'kode.max'        => 'Kode survei maksimal 20 karakter.',
            'kode.unique'     => 'Kode survei sudah terdaftar.',
            'nama.required'   => 'Nama survei wajib diisi.',
            'nama.max'        => 'Nama survei maksimal 255 karakter.',
            'deskripsi.string'=> 'Deskripsi harus berupa teks.',
            'tahun.required'  => 'Tahun survei wajib diisi.',
            'tahun.integer'   => 'Tahun survei harus berupa angka.',
            'periode.max'     => 'Periode maksimal 20 karakter.',
            'status.in'       => 'Status tidak valid. Pilih salah satu: draft, aktif, selesai.',
            'laporan.boolean' => 'Kolom laporan harus bernilai boolean (0/1).',
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
            'kode'      => [$required,'string','max:20',
                                Rule::unique('surveis','kode')
                                    ->ignore($survei->id_survei, 'id_survei')],
            'nama'      => [$required,'string','max:255'],
            'deskripsi' => [$required,'string'], // boleh null saat PATCH karena rule = nullable di atas
            'tahun'     => [$required,'integer'],
            'periode'   => [$required,'string','max:20'],
            'status'    => [$required, Rule::in(['draft','aktif','selesai'])],
            'laporan'   => [$required,'boolean'],
        ], [
            'kode.required'   => 'Kode survei wajib diisi.',
            'kode.max'        => 'Kode survei maksimal 20 karakter.',
            'kode.unique'     => 'Kode survei sudah terdaftar.',
            'nama.required'   => 'Nama survei wajib diisi.',
            'nama.max'        => 'Nama survei maksimal 255 karakter.',
            'deskripsi.string'=> 'Deskripsi harus berupa teks.',
            'tahun.required'  => 'Tahun survei wajib diisi.',
            'tahun.integer'   => 'Tahun survei harus berupa angka.',
            'periode.max'     => 'Periode maksimal 20 karakter.',
            'status.in'       => 'Status tidak valid. Pilih salah satu: draft, aktif, selesai.',
            'laporan.boolean' => 'Kolom laporan harus bernilai boolean (0/1).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = $validator->validated();
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
