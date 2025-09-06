<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RumahTanggaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id_bs'      => 1,
                'nurt'      => 1,
                'krt'       => 'Budi Santoso',
                'keterangan'=> 'Keluarga petani padi',
                'status_sinyal' => 1,
            ],
            [
                'id_bs'      => 1,
                'nurt'      => 2,
                'krt'       => 'Siti Aminah',
                'keterangan'=> 'Usaha warung kelontong',
                'status_sinyal' => 2,
            ],
            [
                'id_bs'      => 2,
                'nurt'      => 1,
                'krt'       => 'Joko Widodo',
                'keterangan'=> 'Pekerja konstruksi',
                'status_sinyal' => 1,
            ],
            [
                'id_bs'      => 2,
                'nurt'      => 2,
                'krt'       => 'Ani Kusuma',
                'keterangan'=> 'Guru sekolah dasar',
                'status_sinyal' => 1,
            ],
            [
                'id_bs'      => 3,
                'nurt'      => 1,
                'krt'       => 'Rahmat Hidayat',
                'keterangan'=> 'Pedagang pasar',
                'status_sinyal' => 2,
            ],
        ];

        foreach ($data as $item) {
            DB::table('rumah_tanggas')->updateOrInsert(
                [
                    'id_bs' => $item['id_bs'],
                    'nurt'  => $item['nurt'],
                ], // kombinasi unik id_bs + nurt
                array_merge($item, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
