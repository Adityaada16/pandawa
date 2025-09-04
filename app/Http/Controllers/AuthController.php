<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name'        => ['required','string','max:100'],
            'email'       => ['required','email','max:255', Rule::unique('users','email')],
            'password'    => ['required','string','min:6','max:255'],
            'nip_pegawai' => ['required','string','min:1','max:50', Rule::unique('users','nip_pegawai')],
        ], [
            //pesan kesalahan
            'name.required'        => 'Nama wajib diisi.',
            'name.max'             => 'Nama maksimal 100 karakter.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.max'            => 'Email maksimal 255 karakter.',
            'email.unique'         => 'Email sudah terdaftar.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 6 karakter.',
            'password.max'         => 'Password maksimal 255 karakter.',
            'nip_pegawai.required' => 'NIP Pegawai wajib diisi.',
            'nip_pegawai.min'      => 'NIP Pegawai minimal 1 karakter.',
            'nip_pegawai.max'      => 'NIP Pegawai maksimal 50 karakter.',
            'nip_pegawai.unique'   => 'NIP Pegawai sudah terdaftar.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'nip_pegawai' => $request->nip_pegawai,
                'id_role' => '3',
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
        $user = User::where('email', $credentials['email'])->first();
    
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
