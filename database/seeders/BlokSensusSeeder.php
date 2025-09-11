<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlokSensusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode'          => 'BS01',
                'kecamatan'     => 'Kecamatan A',
                'desa'          => 'Desa Alpha',
                'nks'           => 'NKS001',
                'id_kab_kota'   => 1,
                'id_petugas_pcl'=> 3,
                'id_petugas_pml'=> 4,
                'id_survei'     => 1,
            ],
            [
                'kode'          => 'BS02',
                'kecamatan'     => 'Kecamatan B',
                'desa'          => 'Desa Beta',
                'nks'           => 'NKS002',
                'id_kab_kota'   => 2,
                'id_petugas_pcl'=> 3,
                'id_petugas_pml'=> 4,
                'id_survei'     => 1,
            ],
            [
                'kode'          => 'BS03',
                'kecamatan'     => 'Kecamatan C',
                'desa'          => 'Desa Gamma',
                'nks'           => 'NKS003',
                'id_kab_kota'   => 3,
                'id_petugas_pcl'=> 3,
                'id_petugas_pml'=> 4,
                'id_survei'     => 2,
            ],
            [
                'kode'          => 'BS04',
                'kecamatan'     => 'Kecamatan D',
                'desa'          => 'Desa Delta',
                'nks'           => 'NKS004',
                'id_kab_kota'   => 4,
                'id_petugas_pcl'=> 3,
                'id_petugas_pml'=> 4,
                'id_survei'     => 2,
            ],
            [
                'kode'          => 'BS05',
                'kecamatan'     => 'Kecamatan E',
                'desa'          => 'Desa Epsilon',
                'nks'           => 'NKS005',
                'id_kab_kota'   => 5,
                'id_petugas_pcl'=> 3,
                'id_petugas_pml'=> 4,
                'id_survei'     => 3,
            ],
        ];

        foreach ($data as $item) {
            DB::table('blok_sensuses')->updateOrInsert(
                [
                    'kode' => $item['kode'],
                    'nks'  => $item['nks'],
                ], // kombinasi unik kode + nks
                array_merge($item, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
