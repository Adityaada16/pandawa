<?php

namespace App\Http\Controllers;

use App\Models\BlokSensus;
use App\Models\Petugas;
use App\Imports\BlokSensusImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\DB;


class BlokSensusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BlokSensus::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pclId = DB::table('roles')->where('name','pcl')->value('id'); // ex: 5
        $pmlId = DB::table('roles')->where('name','pml')->value('id'); // ex: 4

        $validator = Validator::make($request->all(), [
            //validasi untuk tiap atributnya
            'id_survei'     => ['required','integer','exists:surveis,id_survei'],
            'kode'          => ['required','string','size:4'],
            'id_kab_kota'   => ['required','exists:kab_kotas,id_kab_kota'],
            'kecamatan'     => ['required','string','max:50'],
            'desa'          => ['required','string','max:50'],
            'nks'           => ['required','string','max:12', 
                                    Rule::unique('blok_sensuses','nks')
                                        ->where(fn($q) => $q
                                        ->where('kode', $request->input('kode'))),],
            'id_petugas_pcl'=> ['required','integer',
                                    Rule::exists('petugas','id_petugas')
                                        ->where(fn($q) => $q->where('id_role', $pclId)),
                                'different:id_petugas_pml',],
            'id_petugas_pml'=> ['required','integer',
                                    Rule::exists('petugas','id_petugas')
                                        ->where(fn($q) => $q->where('id_role', $pmlId)),
                                'different:id_petugas_pcl',],
        ], [
            // Pesan Kesalahan
            'id_survei.required' => 'Survei wajib diisi.',
            'id_survei.integer'  => 'Survei harus berupa ID angka.',
            'id_survei.exists'   => 'Survei tidak ditemukan.',
            'kode.required'        => 'Kode wajib diisi.',
            'kode.size'            => 'Kode harus tepat 4 karakter.',
            'id_kab_kota.required' => 'Kab/Kota wajib diisi.',
            'id_kab_kota.exists'   => 'Kab/Kota tidak ditemukan.',
            'kecamatan.required'   => 'Kecamatan wajib diisi.',
            'kecamatan.max'        => 'Kecamatan maksimal 50 karakter.',
            'desa.required'        => 'Desa/Kelurahan wajib diisi.',
            'desa.string'          => 'Desa/Kelurahan harus berupa teks.',
            'desa.max'             => 'Desa/Kelurahan maksimal 50 karakter.',
            'nks.required'         => 'NKS wajib diisi.',
            'nks.max'              => 'NKS maksimal 12 karakter.',
            'nks.unique'           => 'NKS sudah digunakan untuk KODE tersebut.',
            'id_petugas_pcl.required'  => 'ID Petugas PCL wajib diisi.',
            'id_petugas_pcl.integer'   => 'ID Petugas PCL harus berupa angka.',
            'id_petugas_pcl.exists'    => 'Petugas PCL tidak valid atau statusnya bukan PCL.',
            'id_petugas_pcl.different' => 'Petugas PCL dan PML tidak boleh sama.',
            'id_petugas_pml.required'  => 'ID Petugas PML wajib diisi.',
            'id_petugas_pml.integer'   => 'ID Petugas PML harus berupa angka.',
            'id_petugas_pml.exists'    => 'Petugas PML tidak valid atau statusnya bukan PML.',
            'id_petugas_pml.different' => 'Petugas PCL dan PML tidak boleh sama.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = $validator->validated();
            $blokSensus = BlokSensus::create($data);

            return response()->json([
                'status'       => 'success',
                'blok_sensus'  => $blokSensus,
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
    public function show(BlokSensus $blokSensus)
    {
        return ['blok_sensus' => $blokSensus];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlokSensus $blokSensus)
    {
       // Kalau PUT => required, kalau PATCH => sometimes
        $required = $request->isMethod('put') ? 'required' : 'sometimes';

        // Ambil role dinamis dari tabel roles
        $pclId = DB::table('roles')->where('name', 'pcl')->value('id'); // ex: 5
        $pmlId = DB::table('roles')->where('name', 'pml')->value('id'); // ex: 4

        $validator = Validator::make($request->all(), [
            'id_survei'   => [$required,'integer','exists:surveis,id_survei'],
            'kode'        => [$required,'string','size:4'],
            'id_kab_kota' => [$required,'integer',Rule::exists('kab_kotas','id_kab_kota')],
            'kecamatan'   => [$required,'string','max:50'], // samakan dgn ukuran kolom di DB
            'desa'        => [$required,'string','max:50'],
            'nks'         => [$required,'string','max:12', 
                                Rule::unique('blok_sensuses', 'nks')
                                    ->where(fn($q) => $q->where('kode', $request->input('kode', $blokSensus->kode)))
                                    ->ignore($blokSensus->id, 'id'), ],
            'id_petugas_pcl' => [$required, 'integer',
                                Rule::exists('petugas', 'id_petugas')
                                    ->where(fn($q) => $q->where('id_role', $pclId)),
                                'different:id_petugas_pml',],
            'id_petugas_pml' => [$required, 'integer',
                                Rule::exists('petugas', 'id_petugas')
                                    ->where(fn($q) => $q->where('id_role', $pmlId)),
                                'different:id_petugas_pcl',],
        ], [
            // Pesan Kesalahan
            'id_survei.integer' => 'Survei harus berupa ID angka.',
            'id_survei.exists'  => 'Survei tidak ditemukan.',    
            'kode.required'        => 'Kode wajib diisi.',
            'kode.size'            => 'Kode harus tepat 4 karakter.',
            'id_kab_kota.required' => 'Kab/Kota wajib diisi.',
            'id_kab_kota.exists'   => 'Kab/Kota tidak ditemukan.',
            'kecamatan.required'   => 'Kecamatan wajib diisi.',
            'kecamatan.max'        => 'Kecamatan maksimal 50 karakter.',
            'desa.required'        => 'Desa/Kelurahan wajib diisi.',
            'desa.string'          => 'Desa/Kelurahan harus berupa teks.',
            'desa.max'             => 'Desa/Kelurahan maksimal 50 karakter.',
            'nks.required'         => 'NKS wajib diisi.',
            'nks.max'              => 'NKS maksimal 12 karakter.',
            'nks.unique'           => 'NKS sudah digunakan untuk KODE tersebut.',
            'id_petugas_pcl.required'  => 'ID Petugas PCL wajib diisi.',
            'id_petugas_pcl.integer'   => 'ID Petugas PCL harus berupa angka.',
            'id_petugas_pcl.exists'    => 'Petugas PCL tidak valid atau statusnya bukan PCL.',
            'id_petugas_pcl.different' => 'Petugas PCL dan PML tidak boleh sama.',
            'id_petugas_pml.required'  => 'ID Petugas PML wajib diisi.',
            'id_petugas_pml.integer'   => 'ID Petugas PML harus berupa angka.',
            'id_petugas_pml.exists'    => 'Petugas PML tidak valid atau statusnya bukan PML.',
            'id_petugas_pml.different' => 'Petugas PCL dan PML tidak boleh sama.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();
            $blokSensus->update($data);
    
            return response()->json([
                'status'       => 'success',
                'blok_sensus'  => $blokSensus->fresh(),
            ], Response::HTTP_OK);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:20480',
        ], [
            //Pesan Kesalahan
            'file.required'  => 'Silakan pilih berkas.',
            'file.mimes'     => 'Format harus berekstensi .xlsx (excel)',
            'file.max'       => 'Ukuran berkas maksimal 20 MB.',
            'file.uploaded'  => 'Upload gagal.',
        ]);

        try {
            Excel::import(new BlokSensusImport(), $request->file('file'));
            return response()->json([
                'status'  => 'success',
                'message' => 'Import blok sensus selesai.',
                'failed'  => 0,
                'errors'  => [],
            ], Response::HTTP_OK);
    
        } catch (ValidationException $e) {
            $failures = collect($e->failures())->map(fn($f) => [
                'row'    => $f->row(),
                'errors' => $f->errors(),
                'values' => $f->values(),
            ])->values();
    
            return response()->json([
                'status'  => 'error',
                'message' => 'data gagal divalidasi.',
                'failed'  => $failures->count(),
                'errors'  => $failures,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
    
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
    public function destroy(BlokSensus $blokSensus)
    {
        $blokSensus->delete();
        return ['message' => 'data blok sensus dihapus'];
    }
}
