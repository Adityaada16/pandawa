<?php

namespace App\Imports;

use App\Models\BlokSensus;
use App\Models\Petugas;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class BlokSensusImport implements ToCollection, ToModel
{
    private $current = 0;

    protected array $petugasCache = [];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    public function model(array $row)
    {
        $this->current++;

        if ($this->current === 1) {
            return null;
        }

        // Ambil & normalisasi nilai dasar
        $idSurvei   = $row[0] ?? null;
        $kode       = isset($row[1]) ? trim((string)$row[1]) : null;
        $idKabKota  = $row[2] ?? null;
        $kecamatan  = isset($row[3]) ? trim((string)$row[3]) : null;
        $desa       = isset($row[4]) ? trim((string)$row[4]) : null;
        $nks        = isset($row[5]) ? trim((string)$row[5]) : null;

        // Email PCL & PML dari kolom [6] dan [7]
        $emailPcl = isset($row[6]) ? strtolower(trim((string)$row[6])) : '';
        $emailPml = isset($row[7]) ? strtolower(trim((string)$row[7])) : '';

        // Resolve email -> id_petugas (pakai cache)
        $idPcl = $this->resolvePetugasIdByEmail($emailPcl);
        $idPml = $this->resolvePetugasIdByEmail($emailPml);

        // Jika email tidak ditemukan di tabel petugas, skip baris ini (hindari FK error)
        if (!$idPcl || !$idPml) {
            return null;
        }

        // Cegah duplikasi: pakai kombinasi (kode, nks) sebagai identitas blok
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
            $blok->id_petugas_pcl = $idPcl; // disimpan sebagai ID hasil lookup email
            $blok->id_petugas_pml = $idPml; // disimpan sebagai ID hasil lookup email
            $blok->save();
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'id_survei'   => ['required','integer','exists:surveis,id_survei'],
            'kode'      => ['required','string','size:4'],
            'kecamatan' => ['required','string','max:50'],
            'desa'      => ['required','string','max:50'],
            'nks'       => ['required','string','max:12',
                                Rule::unique('blok_sensuses','nks')
                                    ->where(fn($q)=>$q
                                    ->where('kode', request('kode'))),],
            'id_kab_kota'    => ['required','integer','exists:kab_kotas,id_kab_kota'],
            'email_pcl'   => [
                'required','email',
                Rule::exists('petugas','email')
                    ->where(fn($q) => $q->whereIn('status', ['pcl']))
            ],
            'email_pml'   => [
                'required','email','different:email_pcl',
                Rule::exists('petugas','email')
                    ->where(fn($q) => $q->whereIn('status', ['pml']))
            ],
        ];
    }

    protected function resolvePetugasIdByEmail(?string $email): ?int
    {
        if (!$email) return null;

        if (!array_key_exists($email, $this->petugasCache)) {
            $this->petugasCache[$email] = Petugas::where('email', $email)->value('id_petugas');
        }

        return $this->petugasCache[$email];
    }
    
    public function uniqueBy() 
    { 
        return ['nks'];
    }
}
