<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'name'     => 'Admin Prov DIY',
                'email'    => 'admin.prov@example.com',
                'password' => Hash::make('password123'),
                'id_role'  => 1, // admin_prov
            ],
            [
                'name'     => 'Admin Kab Bantul',
                'email'    => 'admin.kab@example.com',
                'password' => Hash::make('password123'),
                'id_role'  => 2, // admin_kabKota
            ],
            [
                'name'     => 'Pengguna Biasa',
                'email'    => 'user@example.com',
                'password' => Hash::make('password123'),
                'id_role'  => 3, // user
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], 
                array_merge($user, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }
    }
}
