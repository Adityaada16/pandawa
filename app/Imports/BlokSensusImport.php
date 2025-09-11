<?php

namespace App\Imports;

use App\Models\BlokSensus;
use App\Models\Petugas;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BlokSensusImport implements ToCollection, ToModel, WithValidation, WithStartRow
{
    // cache: email|role_name -> id_petugas
    protected array $petugasByEmailRoleCache = [];

    // cache: role_name -> id
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

        $idSurvei   = $row[0] ?? null;
        $kode       = isset($row[1]) ? trim((string)$row[1]) : null;
        $idKabKota  = $row[2] ?? null;
        $kecamatan  = isset($row[3]) ? trim((string)$row[3]) : null;
        $desa       = isset($row[4]) ? trim((string)$row[4]) : null;
        $nks        = isset($row[5]) ? trim((string)$row[5]) : null;

        // Email PCL & PML dari kolom [6] dan [7]
        $emailPcl = isset($row[6]) ? strtolower(trim((string)$row[6])) : '';
        $emailPml = isset($row[7]) ? strtolower(trim((string)$row[7])) : '';

        // Resolve email -> id_petugas dengan pengecekan ROLE (id_role -> roles.name)
        $idPcl = $this->resolvePetugasIdByEmailAndRole($emailPcl, 'pcl');
        $idPml = $this->resolvePetugasIdByEmailAndRole($emailPml, 'pml');

        // Jika email tidak ditemukan dengan role yang sesuai, skip baris ini (hindari FK error)
        if (!$idPcl || !$idPml) {
            return null;
        }

        // Cegah duplikasi: kombinasi (kode, nks)
        $exists = BlokSensus::where('kode', $kode)
            ->where('nks', $nks)
            ->exists();

        if (!$exists) {
            $blok = new BlokSensus;
            $blok->id_survei      = $idSurvei;
            $blok->kode           = $kode;
            $blok->id_kab_kota    = $idKabKota;
            $blok->kecamatan      = $kecamatan;
            $blok->desa           = $desa;
            $blok->nks            = $nks;
            $blok->id_petugas_pcl = $idPcl; // ID hasil lookup email + role = pcl
            $blok->id_petugas_pml = $idPml; // ID hasil lookup email + role = pml
            $blok->save();
        }

        return null;
    }

    public function rules(): array
    {
        return [
           '0' => ['required','integer','exists:surveis,id_survei'],     // id_survei
            '1' => ['required','string','size:4'],                        // kode
            '2' => ['required','integer','exists:kab_kotas,id_kab_kota'], // id_kab_kota
            '3' => ['required','string','max:50'],                        // kecamatan
            '4' => ['required','string','max:50'],                        // desa
            '5' => ['required','max:12'], // nks

            // 6 = email PCL -> HARUS petugas dengan role 'pcl'
            '6' => ['required','email',
                function ($attribute, $value, $fail) {
                    $email = strtolower(trim((string)$value));
                    $roleId = $this->getRoleIdByName('pcl');
                    $ok = Petugas::where('email', $email)->where('id_role', $roleId)->exists();
                    if (!$ok) $fail('Email PCL tidak ditemukan atau bukan petugas dengan role PCL.');
                },
            ],

            // 7 = email PML -> HARUS petugas dengan role 'pml'
            '7' => ['required','email',
                function ($attribute, $value, $fail) {
                    $email = strtolower(trim((string)$value));
                    $roleId = $this->getRoleIdByName('pml');
                    $ok = Petugas::where('email', $email)->where('id_role', $roleId)->exists();
                    if (!$ok) $fail('Email PML tidak ditemukan atau bukan petugas dengan role PML.');
                },
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'ID Survei wajib diisi.',
            '0.exists'   => 'ID Survei tidak valid.',
            '1.required' => 'Kode wajib diisi.',
            '1.size'     => 'Kode harus 4 karakter.',
            '2.required' => 'Kab/Kota wajib diisi.',
            '2.exists'   => 'Kab/Kota tidak valid.',
            '3.required' => 'Kecamatan wajib diisi.',
            '4.required' => 'Desa wajib diisi.',
            '5.required' => 'NKS wajib diisi.',
            '5.unique'   => 'NKS sudah terdaftar.',
            '6.required' => 'Email PCL wajib diisi.',
            '6.email'    => 'Format email PCL tidak valid.',
            '7.required' => 'Email PML wajib diisi.',
            '7.email'    => 'Format email PML tidak valid.',
        ];
    }

    /**
     * Resolve email + role_name -> id_petugas (cache-aware).
     * Mengembalikan null jika tidak match.
     */
    protected function resolvePetugasIdByEmailAndRole(?string $email, string $roleName): ?int
    {
        if (!$email) 
            return null;
        $email = strtolower(trim($email));
        $roleId = $this->getRoleIdByName($roleName);
        if (!$roleId) return null;

        $key = $email.'|'.$roleId;
        if (!array_key_exists($key, $this->petugasByEmailRoleCache)) {
            $this->petugasByEmailRoleCache[$key] = Petugas::where('email', $email)
                ->where('id_role', $roleId)
                ->value('id_petugas');
        }
        return $this->petugasByEmailRoleCache[$key] ?: null;
    }

    /**
     * Ambil roles.id dari roles.name (cache-aware).
     */
    protected function getRoleIdByName(string $roleName): ?int
    {
        $name = strtolower(trim($roleName));
        if ($name === '') return null;

        if (!array_key_exists($name, $this->roleIdByName)) {
            $this->roleIdByName[$name] = (int) (DB::table('roles')->where('name', $name)->value('id') ?? 0);
        }
        return $this->roleIdByName[$name] ?: null;
    }
    
    public function uniqueBy() 
    { 
        return ['nks','kode'];
    }
}
