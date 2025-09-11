<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PetugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $data = [
            [
                'username'    => 'admin_prov',
                'nama'        => 'Admin Provinsi',
                'email'       => 'adminprov@example.com',
                'password'    => Hash::make('123456'),
                'nip_pegawai' => '19800101-001',
                'id_role'     => 1, // admin_prov
            ],
            [
                'username'    => 'adminkab',
                'nama'        => 'Admin Kabupaten',
                'email'       => 'adminkab@example.com',
                'password'    => Hash::make('123456'),
                'nip_pegawai' => '19800101-002',
                'id_role'     => 2, // admin_kabkota
            ],
            [
                'username'    => 'pcl1',
                'nama'        => 'Petugas PCL 1',
                'email'       => 'pcl1@example.com',
                'password'    => Hash::make('123456'),
                'id_role'     => 5, // pcl
            ],
            [
                'username'    => 'pml1',
                'nama'        => 'Petugas PML 1',
                'email'       => 'pml1@example.com',
                'password'    => Hash::make('123456'),
                'id_role'     => 4, // pml
            ],
            [
                'username'    => 'pengolahan1',
                'nama'        => 'Petugas Pengolahan 1',
                'email'       => 'pengolahan1@example.com',
                'nip_pegawai' => '19800101-003',
                'password'    => Hash::make('123456'),
                'id_role'     => 3, // pengolahan
            ],
        ];

        foreach ($data as $petugas) {
            DB::table('petugas')->updateOrInsert(
                ['email' => $petugas['email']], 
                array_merge($petugas, [
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }
    }
}
