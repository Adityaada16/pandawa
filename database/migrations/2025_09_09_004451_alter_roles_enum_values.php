<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Ubah enum name: hapus 'user', tambah 'pengolahan','pml','pcl'
            DB::statement("ALTER TABLE roles MODIFY COLUMN name ENUM('admin_prov','admin_kabkota','pengolahan','pml','pcl') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Kembalikan ke enum awal
            DB::statement("ALTER TABLE roles MODIFY COLUMN name ENUM('admin_prov','admin_kabkota','user') NOT NULL");
        });
    }
};
