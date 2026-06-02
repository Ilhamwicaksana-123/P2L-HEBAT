<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ms_keranjang_item')) {
            return;
        }

        Schema::create('ms_keranjang_item', function (Blueprint $table) {
            $table->increments('id_keranjang_item');
            $table->integer('id_user');
            $table->integer('id_produk');
            $table->integer('jumlah')->nullable();
            $table->integer('harga_satuan')->nullable();
            $table->timestamps();

            $table->index('id_user', 'fk_ms_keranjang_item_ms_user1_idx');
            $table->index('id_produk', 'fk_ms_keranjang_item_ms_produk1_idx');
            $table->foreign('id_user', 'fk_ms_keranjang_item_ms_user1')
                ->references('id_user')
                ->on('ms_user');
            $table->foreign('id_produk', 'fk_ms_keranjang_item_ms_produk1')
                ->references('id_produk')
                ->on('ms_produk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_keranjang_item');
    }
};
