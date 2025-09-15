<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterSurvei;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MasterSurveiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MasterSurvei::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required','string','max:255'],
        ], [
            'nama.required' => 'Nama survei wajib diisi.',
            'nama.max'      => 'Nama survei maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = $validator->validated();
            $masterSurvei = MasterSurvei::create($data);

            return response()->json([
                'status'       => 'success',
                'master_survei'  => $masterSurvei,
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
    public function show(MasterSurvei $masterSurvei)
    {
        return ['master_survei' => $masterSurvei];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSurvei $masterSurvei)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required','string','max:255'],
        ], [
            'nama.required' => 'Nama survei wajib diisi.',
            'nama.max'      => 'Nama survei maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'error',
                'message'=>$validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Cek apakah ada data yang benar-benar diubah
        $data = $validator->validated();
    
        $changes = false;
        foreach ($data as $key => $value) {
            if ($masterSurvei->$key !== $value) {
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
            $masterSurvei->update($data);
    
            return response()->json([
                'status'       => 'success',
                'master_survei'  => $masterSurvei->fresh(),
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
    public function destroy(MasterSurvei $masterSurvei)
    {
        $masterSurvei->delete();
        return ['message' => 'data master survei berhasil dihapus'];
    }
}
