<?php

namespace App\Imports;

use App\Models\Petugas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PetugasImport implements ToModel, WithValidation, WithStartRow, SkipsEmptyRows, WithUpserts
{
    protected array $roleIdByName = [];

    public function startRow(): int
    {
        return 2; // baris 1 = header
    }

    private function toString($value): ?string
    {
        if ($value === null || $value === '') return null;
        return trim((string)$value);
    }

    public function model(array $row)
    {
        // Default role 'pcl'
        $defaultRoleId = (int) DB::table('roles')->where('name', 'pcl')->value('id');

        // Kolom: 0=username, 1=password, 2=nama, 3=nip_pegawai, 4=fp, 5=email, 6=role_name
        $username    = $this->toString($row[0] ?? null);
        $passwordRaw = $this->toString($row[1] ?? null);
        $nama        = $this->toString($row[2] ?? null);
        $nipPegawai  = $this->toString($row[3] ?? null);
        $fp          = $this->toString($row[4] ?? null);
        $email       = strtolower((string)($this->toString($row[5] ?? null)));
        $roleName    = $this->toString($row[6] ?? null);

        $idRole = $this->resolveRoleIdByName($roleName ?: '', $defaultRoleId);

        $now = now();

        $petugas = new Petugas;
        $petugas->username    = $username;
        $petugas->password    = Hash::make($passwordRaw ?: '123456');
        $petugas->nama        = $nama;
        $petugas->nip_pegawai = $nipPegawai;
        $petugas->fp          = $fp;
        $petugas->email       = $email;    
        $petugas->id_role     = $idRole;
        $petugas->created_at  = $now;       
        $petugas->updated_at  = $now;       

        return $petugas;
    }

    /** Upsert berdasarkan email (harus ada unique index email di DB) */
    public function uniqueBy()
    {
        return 'email';
    }

    /** Kolom yang di-update ketika ada konflik email */
    public function upsertColumns()
    {
        return [
            'username',
            'password',
            'nama',
            'nip_pegawai',
            'fp',
            'id_role',
            'updated_at',
        ];
    }

    public function rules(): array
    {
        return [
            '0' => ['required','max:20'],          // username 
            '1' => ['nullable','min:6','max:50'],  // password
            '2' => ['required','max:50'],          // nama
            '3' => ['nullable','max:50'],          // nip_pegawai 
            '4' => ['nullable','max:255'],         // fp
            '5' => ['required','email','max:255'], // email 
            '6' => ['nullable','max:50'],          // role (nama role)
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Username wajib diisi.',
            '0.max'      => 'Username maksimal 20 karakter.',

            '1.min'      => 'Password minimal 6 karakter.',
            '1.max'      => 'Password maksimal 50 karakter.',

            '2.required' => 'Nama wajib diisi.',
            '2.max'      => 'Nama maksimal 50 karakter.',

            '3.max'      => 'NIP Pegawai maksimal 50 karakter.',

            '4.max'      => 'FP maksimal 255 karakter.',

            '5.required' => 'Email wajib diisi.',
            '5.email'    => 'Format email tidak valid.',
            '5.max'      => 'Email maksimal 255 karakter.',

            '6.max'      => 'Nama role maksimal 50 karakter.',
        ];
    }

    /** roles.name -> roles.id (cache) */
    protected function resolveRoleIdByName(string $roleName, int $defaultRoleId): int
    {
        $name = strtolower(trim($roleName));
        if ($name === '') {
            return $defaultRoleId;
        }

        if (!array_key_exists($name, $this->roleIdByName)) {
            $roleId = DB::table('roles')->where('name', $name)->value('id');
            $this->roleIdByName[$name] = $roleId ? (int) $roleId : 0;
        }

        return $this->roleIdByName[$name] ?: $defaultRoleId;
    }
}
