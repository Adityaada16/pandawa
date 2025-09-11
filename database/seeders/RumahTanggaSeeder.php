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
        $now = now();

        $data = [
            [
                'id_rt'      => 1,
                'id_bs'      => 5,
                'nurt'       => 101,
                'krt'        => 'Budi Santoso',
                'keterangan' => 'Keluarga contoh pertama',
            ],
            [
                'id_rt'      => 2,
                'id_bs'      => 5,
                'nurt'       => 102,
                'krt'        => 'Siti Aminah',
                'keterangan' => 'Keluarga contoh kedua',
            ],
        ];

        foreach ($data as $rt) {
            DB::table('rumah_tanggas')->updateOrInsert(
                ['id_rt' => $rt['id_rt']], // kondisi unik berdasarkan primary key
                array_merge($rt, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }
    }
}
