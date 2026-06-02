<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tr_transaksi')) {
            Schema::create('tr_transaksi', function (Blueprint $table) {
                $table->integer('id_transaksi', true);
                $table->integer('id_pesanan');
                $table->string('kode_order_gateway', 60)->unique('kode_order_gateway_unique');
                $table->string('token_pembayaran', 30);
                $table->integer('total_tagihan')->nullable();
                $table->enum('status_pembayaran', [
                    'menunggu',
                    'berhasil',
                    'kadaluwarsa',
                    'dibatalkan',
                    'ditolak',
                    'dikembalikan',
                ])->default('menunggu');
                $table->timestamps();

                $table->index('id_pesanan', 'fk_tr_transaksi_ms_pesanan1_idx');
                $table->foreign('id_pesanan', 'fk_tr_transaksi_ms_pesanan1')
                    ->references('id_pesanan')
                    ->on('ms_pesanan');
            });
        }

        if (! Schema::hasTable('tr_payment')) {
            Schema::create('tr_payment', function (Blueprint $table) {
                $table->integer('id_payment', true);
                $table->integer('id_transaksi');
                $table->string('metode', 30)->unique('external_id_unique');
                $table->string('nomor_va', 20);
                $table->string('kode_bayar', 20);
                $table->timestamps();

                $table->index('id_transaksi', 'fk_tr_payment_tr_transaksi1_idx');
                $table->foreign('id_transaksi', 'fk_tr_payment_tr_transaksi1')
                    ->references('id_transaksi')
                    ->on('tr_transaksi');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_payment');
        Schema::dropIfExists('tr_transaksi');
    }
};
