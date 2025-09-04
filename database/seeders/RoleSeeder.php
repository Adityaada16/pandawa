<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['name' => 'admin_prov', 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('roles')->updateOrInsert(
            ['id' => 2],
            ['name' => 'admin_kabkota', 'created_at' => $now, 'updated_at' => $now]
        );

        DB::table('roles')->updateOrInsert(
            ['id' => 3],
            ['name' => 'user', 'created_at' => $now, 'updated_at' => $now]
        );
    }
}
