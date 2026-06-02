<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ms_kategori')) {
            Schema::create('ms_kategori', function (Blueprint $table) {
                $table->integer('id_kategori', true);
                $table->string('nama_kategori', 20);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ms_produk')) {
            Schema::create('ms_produk', function (Blueprint $table) {
                $table->integer('id_produk', true);
                $table->integer('id_kategori');
                $table->string('nama_produk', 20);
                $table->integer('harga_produk');
                $table->string('satuan', 10)->nullable();
                $table->integer('stok');
                $table->string('gambar_produk', 70);
                $table->enum('status_produk', ['aktif', 'tidak aktif'])->default('tidak aktif');
                $table->timestamps();

                $table->index('id_kategori', 'fk_ms_produk_ms_kategori1_idx');
                $table->foreign('id_kategori', 'fk_ms_produk_ms_kategori1')
                    ->references('id_kategori')
                    ->on('ms_kategori');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_produk');
        Schema::dropIfExists('ms_kategori');
    }
};
