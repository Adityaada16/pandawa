<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id('id_petugas');
            $table->string('username')->unique();
            $table->string('password')->default('123456'); // default plain-text
            $table->string('nama');
            $table->enum('status', [
                'pcl','pml','edt','pcl_pml','pcl_edt','pml_edt','kasos','admin'
            ])->nullable()->index();
            $table->string('fp')->nullable();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
