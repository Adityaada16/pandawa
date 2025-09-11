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
        Schema::create('pertanyaans', function (Blueprint $table) {
            $table->id('id_pertanyaan');
             // FK ke surveis
            $table->unsignedBigInteger('id_survei');
            $table->foreign('id_survei')
                   ->references('id_survei')->on('surveis')
                   ->cascadeOnUpdate()
                   ->cascadeOnDelete();
            // Label pertanyaan
            $table->string('label', 255);
            $table->enum('pic', ['admin_prov','admin_kabkota','pengolahan','pml','pcl'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->dropForeign(['id_survei']);
        });
        Schema::dropIfExists('pertanyaans');
    }
};
