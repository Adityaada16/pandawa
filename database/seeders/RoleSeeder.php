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

        $roles = [
            ['id' => 1, 'name' => 'admin_prov'],
            ['id' => 2, 'name' => 'admin_kabkota'],
            ['id' => 3, 'name' => 'pengolahan'],
            ['id' => 4, 'name' => 'pml'],
            ['id' => 5, 'name' => 'pcl'],
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $role['id']], // kondisi cek unik
                array_merge($role, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }        
    }
}
