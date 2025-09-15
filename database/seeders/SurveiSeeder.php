<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $data = [
            [
                'id_master_survei' => 3,
                'nama'             => 'SE 2026 - Tahap Pendataan Usaha',
                'tahun'            => 2026,
                'periode'          => '2026-06',
                'status'           => 'draft',
                'laporan'          => false,
                'survei_mulai'     => '2026-06-01',
                'survei_selesai'   => '2026-06-30',
            ],
            [
                'id_master_survei' => 3,
                'nama'             => 'SE 2026 - Verifikasi Lapangan',
                'tahun'            => 2026,
                'periode'          => '2026-08',
                'status'           => 'aktif',
                'laporan'          => true,
                'survei_mulai'     => '2026-08-01',
                'survei_selesai'   => '2026-08-31',
            ],
            [
                'id_master_survei' => 2,
                'nama'             => 'ST 2025 - Pendataan Utama',
                'tahun'            => 2025,
                'periode'          => '2025-05',
                'status'           => 'selesai',
                'laporan'          => true,
                'survei_mulai'     => '2025-05-01',
                'survei_selesai'   => '2025-05-31',
            ],
            [
                'id_master_survei' => 1,
                'nama'             => 'SUSENAS 2025 - Maret',
                'tahun'            => 2025,
                'periode'          => '2025-03',
                'status'           => 'aktif',
                'laporan'          => false,
                'survei_mulai'     => '2025-03-01',
                'survei_selesai'   => '2025-03-31',
            ],
        ];

        foreach ($data as $row) {
            // kunci unik pakai (nama, tahun) agar idempotent
            DB::table('surveis')->updateOrInsert(
                ['nama' => $row['nama'], 'tahun' => $row['tahun']],
                array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
