<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterSurveiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $data = [
            ['nama' => 'Sensus Penduduk'],
            ['nama' => 'Sensus Pertanian'],
            ['nama' => 'Survei Ekonomi'],
        ];

        foreach ($data as $row) {
            DB::table('master_surveis')->updateOrInsert(
                ['nama' => $row['nama']],     // key unik (berdasarkan nama)
                array_merge($row, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
