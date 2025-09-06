<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'username'  => 'pcl1',
                // 'password'  => '123456',
                'nama'      => 'pcl1',
                'status'    => 'pcl',
                // 'fp'  => '12121212',
                'email'     => 'pcl1@gmail.com'
            ],
            [
                'username'  => 'pcl2',
                // 'password'  => '123456',
                'nama'      => 'pcl2',
                'status'    => 'pcl',
                // 'fp'  => '12121212',
                'email'     => 'pcl2@gmail.com'
            ],
            [
                'username'  => 'pml1',
                // 'password'  => '123456',
                'nama'      => 'pml1',
                'status'    => 'pml',
                // 'fp'  => '12121212',
                'email'     => 'pml1@gmail.com'
            ],
            [
                'username'  => 'pml2',
                // 'password'  => '123456',
                'nama'      => 'pml2',
                'status'    => 'pml',
                // 'fp'  => '12121212',
                'email'     => 'pml2@gmail.com'
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
