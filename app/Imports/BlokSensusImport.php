<?php

namespace App\Imports;

use App\Models\BlokSensus;
use App\Models\Petugas;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithUpserts;

class BlokSensusImport implements ToModel, WithValidation, WithStartRow, SkipsEmptyRows, WithUpserts
{
    // cache: email|role_name -> id_petugas
    protected array $petugasByEmailRoleCache = [];
    // cache: role_name -> id
    protected array $roleIdByName = [];
    // cache: kode kab/kota (4 digit) -> id_kab_kota
    protected array $kabkotaIdByKode = [];

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
        // Kolom Excel:
        // 0=id_survei, 1=kodeBS, 2=KODE KAB 4 digit, 3=kecamatan, 4=desa, 5=nks, 6=sls, 7=email PCL, 8=email PML
        $idSurvei   = (int)($row[0] ?? 0);
        $kodeBS     = $this->toString($row[1] ?? null);
        $kodeKab    = $this->toString($row[2] ?? null);
        $kecamatan  = $this->toString($row[3] ?? null);
        $desa       = $this->toString($row[4] ?? null);
        $nks        = $this->toString($row[5] ?? null);
        $sls        = $this->toString($row[6] ?? null);
        $emailPcl   = strtolower((string)($this->toString($row[7] ?? null)));
        $emailPml   = strtolower((string)($this->toString($row[8] ?? null)));

        // Lookup FK
        $idKabKota = $this->resolveKabKotaIdByKode($kodeKab);
        $idPcl     = $this->resolvePetugasIdByEmailAndRole($emailPcl, 'pcl');
        $idPml     = $this->resolvePetugasIdByEmailAndRole($emailPml, 'pml');

        $now = now();

        $blok = new BlokSensus;
        $blok->id_survei       = $idSurvei;
        $blok->kode            = $kodeBS;
        $blok->id_kab_kota     = (int)$idKabKota;
        $blok->kecamatan       = $kecamatan;
        $blok->desa            = $desa;
        $blok->sls             = $sls;             
        $blok->nks             = $nks;
        $blok->id_petugas_pcl  = (int)$idPcl;
        $blok->id_petugas_pml  = (int)$idPml;
        $blok->created_at      = $now;              
        $blok->updated_at      = $now;              

        return $blok;
    }

    public function uniqueBy()
    {
        return ['id_survei', 'id_kab_kota', 'kecamatan', 'desa', 'sls', 'nks'];
    }

    /** Kolom yang akan di-UPDATE saat terjadi konflik unik */
    public function upsertColumns()
    {
        return [
            'kode',
            'id_petugas_pcl',
            'id_petugas_pml',
            'updated_at',
        ];
    }

    public function rules(): array
    {
        return [
            '0' => ['required','exists:surveis,id_survei'],       // id_survei
            '1' => ['required','size:4'],                           // kode BS
            '2' => ['required','size:4','exists:kab_kotas,kode'],   // KODE KAB/KOTA (4 digit)
            '3' => ['required','max:50'],                           // kecamatan
            '4' => ['required','max:50'],                           // desa
            '5' => ['required','max:12'],                           // nks
            '6' => ['required','max:12'],                           // sls (WAJIB)
            '7' => ['required','email', function ($attribute, $value, $fail) {
                $email = strtolower(trim((string)$value));
                $roleId = $this->getRoleIdByName('pcl');
                if (!Petugas::where('email', $email)->where('id_role', $roleId)->exists()) {
                    $fail('Email PCL tidak ditemukan atau bukan petugas dengan role PCL.');
                }
            }],
            '8' => ['required','email', function ($attribute, $value, $fail) {
                $email = strtolower(trim((string)$value));
                $roleId = $this->getRoleIdByName('pml');
                if (!Petugas::where('email', $email)->where('id_role', $roleId)->exists()) {
                    $fail('Email PML tidak ditemukan atau bukan petugas dengan role PML.');
                }
            }],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'ID Survei wajib diisi.',
            '0.exists'   => 'ID Survei tidak valid.',
            '1.required' => 'Kode wajib diisi.',
            '1.size'     => 'Kode harus 4 karakter.',
            '2.required' => 'Kode Kab/Kota wajib diisi.',
            '2.size'     => 'Kode Kab/Kota harus 4 digit.',
            '2.exists'   => 'Kode Kab/Kota tidak valid.',
            '3.required' => 'Kecamatan wajib diisi.',
            '4.required' => 'Desa wajib diisi.',
            '5.required' => 'NKS wajib diisi.',
            '6.required' => 'SLS wajib diisi.',
            '7.required' => 'Email PCL wajib diisi.',
            '7.email'    => 'Format email PCL tidak valid.',
            '8.required' => 'Email PML wajib diisi.',
            '8.email'    => 'Format email PML tidak valid.',
        ];
    }

    /** Helpers */
    protected function resolvePetugasIdByEmailAndRole(?string $email, string $roleName): ?int
    {
        if (!$email) return null;
        $email  = strtolower(trim($email));
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

    protected function getRoleIdByName(string $roleName): ?int
    {
        $name = strtolower(trim($roleName));
        if ($name === '') return null;

        if (!array_key_exists($name, $this->roleIdByName)) {
            $roleId = DB::table('roles')->where('name', $name)->value('id');
            $this->roleIdByName[$name] = $roleId ? (int)$roleId : 0;
        }
        return $this->roleIdByName[$name] ?: null;
    }

    protected function resolveKabKotaIdByKode(?string $kode): ?int
    {
        if (!$kode) return null;
        $kode = trim((string)$kode);

        if (!array_key_exists($kode, $this->kabkotaIdByKode)) {
            $this->kabkotaIdByKode[$kode] = DB::table('kab_kotas')
                ->where('kode', $kode)
                ->value('id_kab_kota');
        }
        $val = $this->kabkotaIdByKode[$kode] ?? null;
        return $val ? (int)$val : null;
    }
}
