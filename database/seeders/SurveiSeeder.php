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
        $data = [
            [
                'kode'       => 'SE2026',
                'nama'       => 'Sensus Ekonomi 2026',
                'deskripsi'  => 'Sensus untuk mengumpulkan data kegiatan ekonomi di seluruh Indonesia.',
                'tahun'      => 2026,
                'periode'    => 'Tahap Persiapan',
                'tgl_mulai'  => '2025-01-01',
                'tgl_selesai'=> '2026-12-31',
                'status'     => 'draft',
            ],
            [
                'kode'       => 'ST2025',
                'nama'       => 'Sensus Pertanian 2025',
                'deskripsi'  => 'Pendataan sektor pertanian meliputi tanaman pangan, hortikultura, perkebunan, peternakan, kehutanan, dan perikanan.',
                'tahun'      => 2025,
                'periode'    => '2025-05',
                'tgl_mulai'  => '2025-05-01',
                'tgl_selesai'=> '2025-05-30',
                'status'     => 'aktif',
            ],
            [
                'kode'       => 'SP2020',
                'nama'       => 'Sensus Penduduk 2020',
                'deskripsi'  => 'Pendataan penduduk untuk memperoleh data demografi dasar.',
                'tahun'      => 2020,
                'periode'    => '2020-09',
                'tgl_mulai'  => '2020-09-01',
                'tgl_selesai'=> '2020-09-30',
                'status'     => 'selesai',
            ],
        ];

        foreach ($data as $item) {
            DB::table('surveis')->updateOrInsert(
                ['kode' => $item['kode']], // key unik
                array_merge($item, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
