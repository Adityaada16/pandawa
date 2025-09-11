<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pertanyaan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PertanyaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Pertanyaan::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_survei' => ['required','integer',
                                Rule::exists('surveis','id_survei')
                                    ->where(fn($q) => $q
                                        ->where('laporan', 1)),],
            'label' => ['required','string','max:255'],
            'pic'   => ['nullable','string','exists:roles,name'], // nama role valid
        ], [
            'id_survei.required' => 'Survei wajib diisi.',
            'id_survei.exists'   => 'Survei tidak valid atau belum aktif untuk laporan.',
            'label.required'     => 'Label pertanyaan wajib diisi.',
            'label.max'          => 'Label maksimal 255 karakter.',
            'pic.exists'         => 'PIC tidak valid. Pilih salah satu nama role yang ada.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();
            $pertanyaan = Pertanyaan::create($data);
    
            return response()->json([
                'status' => 'success',
                'survei' => $pertanyaan,
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
    public function show(Pertanyaan $pertanyaan)
    {
        return ['pertanyaan' => $pertanyaan];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pertanyaan $pertanyaan)
    {
        $required = $request->isMethod('put') ? 'required' : 'nullable';

        $validator = Validator::make($request->all(), [
            'id_survei' => [$required,'integer',
                                Rule::exists('surveis','id_survei')
                                    ->where(fn($q) => $q
                                    ->where('laporan', 1)),],
            'label' => [$required,'string','max:255'],
            'pic'   => [$required,'string','exists:roles,name'],
        ], [
            'id_survei.required' => 'Survei wajib diisi.',
            'id_survei.exists'   => 'Survei tidak valid atau belum aktif untuk laporan.',
            'label.required'     => 'Label pertanyaan wajib diisi.',
            'label.max'          => 'Label maksimal 255 karakter.',
            'pic.exists'         => 'PIC tidak valid. Pilih salah satu nama role yang ada.',
        ]);
    
        if ($validator->fails()) {
             return response()->json([
                 'status'  => 'error',
                 'message' => $validator->errors(),
             ], Response::HTTP_BAD_REQUEST);
        }
 
        try {
             $data = $validator->validated();
             $pertanyaan->update($data);
 
             return response()->json([
                 'status' => 'success',
                 'pertanyaan' => $pertanyaan->fresh(), // ambil nilai terbaru dari DB
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
    public function destroy(Pertanyaan $pertanyaan)
    {
        $pertanyaan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Pertanyaan berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
