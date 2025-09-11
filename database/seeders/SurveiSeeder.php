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
                'kode'      => 'SE2026',
                'nama'      => 'Survei Ekonomi 2026',
                'deskripsi' => 'Survei untuk mengumpulkan data kegiatan ekonomi di seluruh Indonesia.',
                'tahun'     => 2026,
                'periode'   => 'Tahap Persiapan',
                'status'    => 'draft',
                // 'laporan'   => 0,
            ],
            [
                'kode'      => 'ST2023',
                'nama'      => 'Survei Pertanian 2023',
                'deskripsi' => 'Survei sektor pertanian meliputi tanaman pangan, hortikultura, perkebunan, peternakan, kehutanan, dan perikanan.',
                'tahun'     => 2025,
                'periode'   => '2025-05',
                'status'    => 'aktif',
                'laporan'   => 0,
            ],
            [
                'kode'      => 'SP2020',
                'nama'      => 'Survei Penduduk 2020',
                'deskripsi' => 'Survei penduduk untuk memperoleh data demografi dasar.',
                'tahun'     => 2020,
                'periode'   => '2020-09',
                'status'    => 'selesai',
                'laporan'   => 1,
            ],
        ];

        foreach ($data as $item) {
            DB::table('surveis')->updateOrInsert(
                ['kode' => $item['kode']], // key unik
                array_merge($item, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }
    }
}
