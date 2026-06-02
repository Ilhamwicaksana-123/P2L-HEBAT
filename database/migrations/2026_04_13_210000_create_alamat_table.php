<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ms_alamat')) {
            return;
        }

        Schema::create('ms_alamat', function (Blueprint $table) {
            $table->integer('id_alamat', true);
            $table->integer('id_user');
            $table->string('nama_penerima', 30);
            $table->string('no_hp', 15);
            $table->string('alamat', 70);
            $table->string('kota', 10);
            $table->string('kode_pos', 6);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('id_user', 'fk_ms_alamat_ms_user_idx');
            $table->foreign('id_user', 'fk_ms_alamat_ms_user')
                ->references('id_user')
                ->on('ms_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_alamat');
    }
};
