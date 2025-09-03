<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabKotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('kab_kotas')->insert([
            ['kode' => '3401', 'name' => 'kulon_progo', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => '3402', 'name' => 'bantul',       'created_at' => $now, 'updated_at' => $now],
            ['kode' => '3403', 'name' => 'gunungkidul',  'created_at' => $now, 'updated_at' => $now],
            ['kode' => '3404', 'name' => 'sleman',       'created_at' => $now, 'updated_at' => $now],
            ['kode' => '3471', 'name' => 'yogyakarta',   'created_at' => $now, 'updated_at' => $now], // Kota
        ]);
    }
}
