<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ms_pesanan_detail')) {
            return;
        }

        Schema::create('ms_pesanan_detail', function (Blueprint $table) {
            $table->integer('id_pesanan_detail', true);
            $table->integer('id_pesanan');
            $table->integer('id_produk');
            $table->string('nama_produk', 20);
            $table->integer('harga_produk');
            $table->integer('jumlah_barang');
            $table->integer('subtotal');
            $table->timestamps();

            $table->index('id_pesanan', 'fk_ms_pesanan_detail_ms_pesanan1_idx');
            $table->index('id_produk', 'fk_ms_pesanan_detail_ms_produk1_idx');
            $table->foreign('id_pesanan', 'fk_ms_pesanan_detail_ms_pesanan1')
                ->references('id_pesanan')
                ->on('ms_pesanan');
            $table->foreign('id_produk', 'fk_ms_pesanan_detail_ms_produk1')
                ->references('id_produk')
                ->on('ms_produk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_pesanan_detail');
    }
};
