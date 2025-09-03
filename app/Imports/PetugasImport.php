<?php

namespace App\Imports;

use App\Models\Petugas;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class PetugasImport implements ToCollection, ToModel
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
            $count = Petugas::where('username', '=', $row[0])->count();
            if(empty($count))
            {
                $petugas = new Petugas;
                $petugas -> username = $row[0];
                $petugas -> password = $row[1];
                $petugas -> nama = $row[2];
                $petugas -> status = $row[3];
                $petugas -> fp = $row[4];
                $petugas -> email = $row[5];
                $petugas -> save();
            }
        }
    }

    public function rules(): array
    {
        return [
            'username' => ['required','string','max:20', Rule::unique('petugas','username')],
            'password' => ['required','string','max:50'], 
            'nama'     => ['required','string','max:50'],
            'status'   => ['nullable', Rule::in(['pcl','pml','edt','pcl_pml','pcl_edt','pml_edt','kasos','admin'])],
            'fp'       => ['nullable','string','max:255'],
            'email'    => ['required','email','max:255', Rule::unique('petugas','email')],
        ];
    }

    public function uniqueBy() 
    { 
        return ['username','email'];
    }
}
