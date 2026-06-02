<?php

use App\Models\Pesanan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->string('name', 60);
                $table->string('description', 160)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedTinyInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();
        $methods = [
            [
                'code' => Pesanan::METODE_TRANSFER,
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran melalui simulasi bank di Midtrans.',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => Pesanan::METODE_E_WALLET,
                'name' => 'E-Wallet',
                'description' => 'Pembayaran digital melalui simulasi Midtrans.',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => Pesanan::METODE_COD,
                'name' => 'COD',
                'description' => 'Bayar langsung saat pesanan diterima.',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                $method
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
