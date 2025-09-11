<?php

namespace App\Imports;

use App\Models\Petugas;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PetugasImport implements ToCollection, ToModel, WithValidation, WithStartRow
{
    // private $current = 0;
    protected array $roleIdByName = [];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    public function startRow(): int
    {
        return 2; // baris 1 = header, validasi & model mulai baris 2
    }

    public function model(array $row)
    {
        // $this->current++;
        // if ($this->current === 1) {
        //     // baris pertama header
        //     return null;
        // }

        // Default role 'pcl'
        $defaultRoleId = (int) DB::table('roles')->where('name', 'pcl')->value('id');

        // Cegah duplikasi by email
        $email = isset($row[5]) ? strtolower(trim((string)$row[5])) : null;
        if (!$email || Petugas::where('email', $email)->exists()) {
            return null;
        }

        // Index: 0=username, 1=password, 2=nama, 3=nip_pegawai, 4=fp, 5=email, 6=role (by nama)
        $username    = isset($row[0]) ? trim((string)$row[0]) : null;
        $passwordRaw = isset($row[1]) ? (string)$row[1] : '';
        $nama        = isset($row[2]) ? trim((string)$row[2]) : null;
        $nipPegawai  = isset($row[3]) && $row[3] !== '' ? trim((string)$row[3]) : null;
        $fp          = isset($row[4]) && $row[4] !== '' ? trim((string)$row[4]) : null;
        $roleName    = isset($row[6]) ? strtolower(trim((string)$row[6])) : '';

        // Resolve role name -> id
        $idRole = $this->resolveRoleIdByName($roleName, $defaultRoleId);

        $petugas = new Petugas;
        $petugas->username    = $username;
        $petugas->password    = Hash::make($passwordRaw !== '' ? $passwordRaw : '123456');
        $petugas->nama        = $nama;
        $petugas->nip_pegawai = $nipPegawai ?: null;
        $petugas->fp          = $fp ?: null;
        $petugas->email       = $email;
        $petugas->id_role     = $idRole;

        $petugas->save();
        return null;
    }

    public function rules(): array
    {
        return [
            '0' => ['required','string','max:20', Rule::unique('petugas','username')], // username
            '1' => ['nullable','string','min:6','max:50'],                              // password
            '2' => ['required','string','max:50'],                                      // nama
            '3' => ['nullable','min:1','max:50', Rule::unique('petugas','nip_pegawai')], // nip_pegawai
            '4' => ['nullable','string','max:255'],                                     // fp
            '5' => ['required','email','max:255', Rule::unique('petugas','email')],     // email
            '6' => ['nullable','string','max:50', Rule::exists('roles','name')],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Username wajib diisi.',
            '0.max'      => 'Username maksimal 20 karakter.',
            '0.unique'   => 'Username sudah digunakan.',

            '1.min'      => 'Password minimal 6 karakter.',
            '1.max'      => 'Password maksimal 50 karakter.',

            '2.required' => 'Nama wajib diisi.',
            '2.max'      => 'Nama maksimal 50 karakter.',

            '3.min'      => 'NIP Pegawai minimal 1 karakter.',
            '3.max'      => 'NIP Pegawai maksimal 50 karakter.',
            '3.unique'   => 'NIP Pegawai sudah terdaftar.',

            '4.max'      => 'FP maksimal 255 karakter.',

            '5.required' => 'Email wajib diisi.',
            '5.email'    => 'Format email tidak valid.',
            '5.max'      => 'Email maksimal 255 karakter.',
            '5.unique'   => 'Email sudah terdaftar.',

            '6.required' => 'Nama role wajib diisi.',
            '6.exists'   => 'Nama role tidak ditemukan di tabel roles.',
        ];
    }

    public function uniqueBy() 
    { 
        return 'email';
    }

    /**
     * Resolve roles.name -> roles.id (pakai cache), fallback ke default.
     */
    protected function resolveRoleIdByName(string $roleName, int $defaultRoleId): int
    {
        $name = strtolower(trim($roleName));
        if ($name === '') {
            return $defaultRoleId;
        }

        if (!array_key_exists($name, $this->roleIdByName)) {
            $this->roleIdByName[$name] = (int) (DB::table('roles')->where('name', $name)->value('id') ?? 0);
        }

        return $this->roleIdByName[$name] ?: $defaultRoleId;
    }
}
