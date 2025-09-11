<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $data = [
            ['id_survei'=>3, 'label'=>'Apakah sample memenuhi target?', 'pic'=>'pml'],
            ['id_survei'=>3, 'label'=>'Apakah listing usaha lengkap?',     'pic'=>'pcl'],
        ];
        
        foreach ($data as $pertanyaan) {
            DB::table('pertanyaans')->updateOrInsert(
                ['label' => $pertanyaan['label']], // kondisi cek unik
                array_merge($pertanyaan, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }        
    }
}
