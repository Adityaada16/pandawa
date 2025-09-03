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
            'kode'        => 'required|string|max:20|unique:surveis,kode',
            'nama'        => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'tahun'       => 'required|integer|min:2000|max:2100',
            'periode'     => 'nullable|string|max:20',
            'tgl_mulai'   => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'status'      => ['nullable', Rule::in(['draft','aktif','selesai'])],
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
        $validator = Validator::make($request->all(), [
        'kode'        => ['sometimes','string','max:20', 
            Rule::unique('surveis','kode')->ignore($survei->id_survei, 'id_survei'),],
        'nama'        => ['sometimes','string','max:255'],
        'deskripsi'   => ['sometimes','nullable','string'],
        'tahun'       => ['sometimes','integer','min:2000','max:2100'],
        'periode'     => ['sometimes','nullable','string','max:20'],
        'tgl_mulai'   => ['sometimes','nullable','date'],
        'tgl_selesai' => ['sometimes','nullable','date','after_or_equal:tgl_mulai'],
        'status'      => ['sometimes', Rule::in(['draft','aktif','selesai'])]
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
