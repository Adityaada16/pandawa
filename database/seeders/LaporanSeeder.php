<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $pertanyaan = DB::table('pertanyaans')
            ->where('id_survei', 3)
            ->pluck('id_pertanyaan','label');

        $data = [
            [
                'id_rumah_tangga' => 1,
                'id_pertanyaan'   => $pertanyaan['Apakah sample memenuhi target?'] ?? null,
                'jawaban'         => 'Ya, sudah memenuhi target.',
            ],
            [
                'id_rumah_tangga' => 1,
                'id_pertanyaan'   => $pertanyaan['Apakah listing usaha lengkap?'] ?? null,
                'jawaban'         => 'Lengkap semua.',
            ],
            [
                'id_rumah_tangga' => 2,
                'id_pertanyaan'   => $pertanyaan['Apakah sample memenuhi target?'] ?? null,
                'jawaban'         => 'Belum, masih kurang.',
            ],
        ];

        foreach ($data as $laporan) {
            if ($laporan['id_pertanyaan']) {
                DB::table('laporans')->updateOrInsert(
                    [
                        'id_rumah_tangga' => $laporan['id_rumah_tangga'],
                        'id_pertanyaan'   => $laporan['id_pertanyaan'],
                    ],
                    [
                        'jawaban'    => $laporan['jawaban'],
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
