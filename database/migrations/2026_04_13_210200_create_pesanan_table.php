<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ms_pesanan')) {
            return;
        }

        Schema::create('ms_pesanan', function (Blueprint $table) {
            $table->integer('id_pesanan', true);
            $table->integer('id_user');
            $table->integer('id_alamat');
            $table->string('kode_pesanan', 15)->unique('kode_pesanan_unique');
            $table->enum('status_pesanan', ['menunggu pembayaran', 'diproses', 'dikirim', 'selesai', 'batal']);
            $table->enum('metode_pembayaran', ['transfer', 'e_wallet', 'cod']);
            $table->integer('total_harga');
            $table->timestamps();

            $table->unique('id_pesanan', 'id_pesanan_unique');
            $table->index('id_user', 'fk_ms_pesanan_ms_user1_idx');
            $table->index('id_alamat', 'fk_ms_pesanan_ms_alamat1_idx');
            $table->foreign('id_user', 'fk_ms_pesanan_ms_user1')
                ->references('id_user')
                ->on('ms_user');
            $table->foreign('id_alamat', 'fk_ms_pesanan_ms_alamat1')
                ->references('id_alamat')
                ->on('ms_alamat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_pesanan');
    }
};
