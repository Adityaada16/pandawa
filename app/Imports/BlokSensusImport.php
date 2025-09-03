<?php

namespace App\Imports;

use App\Models\BlokSensus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class BlokSensusImport implements ToCollection, ToModel
{
    private $current = 0;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    public function model(array $row)
    {
        $this -> current++;
        if($this -> current > 1)
        {
            $count = BlokSensus::where('nks', '=', $row[4])->count();
            if(empty($count))
            {
                $blokSensus = new BlokSensus;
                $blokSensus -> kode = $row[0];
                $blokSensus -> id_kab_kota = $row[1];
                $blokSensus -> kecamatan = $row[2];
                $blokSensus -> desa = $row[3];
                $blokSensus -> nks = $row[4];
                $blokSensus -> id_petugas_pcl = $row[5];
                $blokSensus -> id_petugas_pml = $row[6];
                $blokSensus -> save();
            }
        }
    }

    public function rules(): array
    {
        return [
            'kode'      => ['required','string','size:4'],
            'kecamatan' => ['required','string','max:50'],
            'desa'      => ['required','string','max:50'],
            'nks'       => ['required','string','max:12',
                                Rule::unique('blok_sensuses','nks')
                                    ->where(fn($q)=>$q
                                    ->where('kode', request('kode'))),],
            'id_kab_kota'    => ['required','integer','exists:kab_kotas,id_kab_kota'],
            'id_petugas_pcl' => ['required','integer',
                                    Rule::exists('petugas','id_petugas')
                                        ->where(fn($q)=>$q
                                        ->whereIn('status',['pcl'])),
                                    'different:id_petugas_pml', ],
            'id_petugas_pml' => ['required','integer',
                                    Rule::exists('petugas','id_petugas')
                                        ->where(fn($q)=>$q
                                        ->whereIn('status',['pml','pcl_pml','pml_edt'])),
                                    'different:id_petugas_pcl',],
        ];
    }

    public function uniqueBy() 
    { 
        return ['nks'];
    }
}
