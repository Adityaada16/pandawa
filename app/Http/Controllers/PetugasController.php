<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Imports\PetugasImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Petugas::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'username' => ['required','string','max:20', Rule::unique('petugas','username')],
        //     'password' => ['nullable','string','max:50', 'min:6'], // default 123456 kalau kosong
        //     'nama'     => ['required','string','max:50'],
        //     'status'   => ['nullable', Rule::in(['pcl','pml','edt','pcl_pml','pcl_edt','pml_edt','kasos','admin'])],
        //     'fp'       => ['nullable','string','max:255'],
        //     'email'    => ['required','email','max:255', Rule::unique('petugas','email')],
        // ], [
        //     //pesan kesalahan
        //     'username.required' => 'Username wajib diisi.',
        //     'username.max'      => 'Username maksimal 20 karakter.',
        //     'username.unique'   => 'Username sudah digunakan.',
        //     'password.max'      => 'Password maksimal 50 karakter.',
        //     'password.min'      => 'password minimal 6 karakter.',
        //     'nama.required'     => 'Nama wajib diisi.',
        //     'nama.max'          => 'Nama maksimal 50 karakter.',
        //     'status.in'         => 'Status tidak valid. Pilih salah satu: pcl, pml, edt, pcl_pml, pcl_edt, pml_edt, kasos, admin.',
        //     'fp.max'            => 'FP maksimal 255 karakter.',
        //     'email.email'       => 'Format email tidak valid.',
        //     'email.max'         => 'Email maksimal 255 karakter.',
        //     'email.unique'      => 'Email sudah digunakan.',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => $validator->errors(),
        //     ], Response::HTTP_BAD_REQUEST);
        // }

        // try {
        //     $data = $validator->validated();
        //     // default password kalau kosong 123456
        //     if (empty($data['password'])) {
        //         $data['password'] = '123456';
        //     }

        //     $petugas = Petugas::create($data);

        //     return response()->json([
        //         'status'  => 'success',
        //         'petugas' => $petugas,
        //     ], Response::HTTP_OK);
        // } catch (\Throwable $e) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => $e->getMessage(),
        //     ], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Petugas $petugas)
    {
        return ['petugas' => $petugas];
    }
    
    public function update(Request $request, Petugas $petugas)
    {
        // Kalau PUT => required, kalau PATCH => sometimes
        $required = $request->isMethod('put') ? 'required' : 'sometimes';

        $validator = Validator::make($request->all(), [
            'username'      => [$required,'string','max:20',
                                    Rule::unique('petugas','username')
                                        ->ignore($petugas->id_petugas, 'id_petugas'),],
            'password'      => [$required,'nullable','string','max:50','min:6'],
            'nama'          => [$required,'string','max:50'],
            'fp'            => [$required,'nullable','string','max:255'],
            'email'         => [$required,'email','max:255',
                                    Rule::unique('petugas','email')
                                        ->ignore($petugas->id_petugas, 'id_petugas'), ],
            'nip_pegawai'   => [$required, 'nullable', 'string', 'min:1', 'max:50',
                                    Rule::unique('petugas','nip_pegawai')
                                        ->ignore($petugas->id_petugas,'id_petugas'),],
            'id_role'       => [$required, 'nullable', 'integer', 'exists:roles,id'],
        ], [
            //pesan kesalahan
            'username.required' => 'Username wajib diisi.',
            'username.max'      => 'Username maksimal 20 karakter.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.max'      => 'Password maksimal 50 karakter.',
            'password.min'      => 'password minimal 6 karakter.',
            'nama.required'     => 'Nama wajib diisi.',
            'nama.max'          => 'Nama maksimal 50 karakter.',
            'fp.max'            => 'FP maksimal 255 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.max'         => 'Email maksimal 255 karakter.',
            'email.unique'      => 'Email sudah digunakan.',
            'nip_pegawai.min'   => 'NIP Pegawai minimal 1 karakter.',
            'nip_pegawai.max'   => 'NIP Pegawai maksimal 50 karakter.',
            'nip_pegawai.unique'=> 'NIP Pegawai sudah terdaftar.',
            'id_role.exists'    => 'Role tidak valid.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();
    
            // kalau password tidak dikirim atau string kosong tidak di update
            if (!array_key_exists('password', $data) || $data['password'] === null || $data['password'] === '') {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }
    
            $petugas->update($data);
    
            return response()->json([
                'status'  => 'success',
                'petugas' => $petugas->fresh(),
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
            Excel::import(new PetugasImport(), $request->file('file'));

            return response()->json([
                'status'  => 'success',
                'message' => 'Import petugas selesai.',
                'failed'  => 0,
                'errors'  => [],
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            $failures = collect($e->failures())->map(fn($f) => [
                'row'    => $f->row(),     // header = 1
                'errors' => $f->errors(),  // pesan validasi
                'values' => $f->values(),  // data baris
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
    public function destroy(Petugas $petugas)
    {
        $petugas->delete();
        return ['message' => 'data petugas dihapus'];
    }
}
