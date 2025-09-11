<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use App\Models\Petugas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'username'    => ['required','string','max:20', Rule::unique('petugas','username')],
            'nama'        => ['required','string','max:50'],
            'email'       => ['required','email','max:255', Rule::unique('petugas','email')],
            'password'    => ['nullable','string','min:6','max:50'],
            'fp'          => ['nullable','string','max:255'],
            'nip_pegawai' => ['nullable','string','min:1','max:50', Rule::unique('petugas','nip_pegawai')],
            'id_role'     => ['nullable','integer','exists:roles,id'],
        ], [
            //pesan kesalahan
           'username.required'    => 'Username wajib diisi.',
            'username.max'        => 'Username maksimal 20 karakter.',
            'username.unique'     => 'Username sudah digunakan.',
            'password.max'        => 'Password maksimal 50 karakter.',
            'password.min'        => 'Password minimal 6 karakter.',
            'nama.required'       => 'Nama wajib diisi.',
            'nama.max'            => 'Nama maksimal 50 karakter.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.max'           => 'Email maksimal 255 karakter.',
            'email.unique'        => 'Email sudah terdaftar.',
            'nip_pegawai.min'     => 'NIP Pegawai minimal 1 karakter.',
            'nip_pegawai.max'     => 'NIP Pegawai maksimal 50 karakter.',
            'nip_pegawai.unique'  => 'NIP Pegawai sudah terdaftar.',
            'fp.max'              => 'FP maksimal 255 karakter.',
            'id_role.exists'      => 'Role tidak valid.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $data = $validator->validated();

            // Default password kalau kosong
            if (empty($data['password'])) {
                $data['password'] = '123456';
            }
  
            // Default role = 'pcl' (ambil id dari tabel roles)
            if (empty($data['id_role'])) {
                $defaultRoleId = DB::table('roles')->where('name','pcl')->value('id');
                $data['id_role'] = $defaultRoleId;
            }

            $user = Petugas::create([
                'username'    => $data['username'],
                'nama'        => $data['nama'],
                'email'       => $data['email'],
                'password'    => Hash::make($data['password']),
                'fp'          => $data['fp'] ?? null,
                'nip_pegawai' => $data['nip_pegawai'] ?? null,
                'id_role'     => $data['id_role'],
            ]);
    
            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return 'register';
    }
    

    public function login(Request $request) 
    {
        // Validasi permintaan
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], Response::HTTP_BAD_REQUEST);
        }
    
        // Ambil kredensial hanya setelah validasi
        $credentials = $request->only('email', 'password');
        $user = Petugas::where('email', $credentials['email'])->first();
    
        // Cek keberadaan pengguna dan password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ], Response::HTTP_UNAUTHORIZED);
        }
    
        // Jika berhasil login, buat token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Anda telah logout.'
        ];
    }

}
