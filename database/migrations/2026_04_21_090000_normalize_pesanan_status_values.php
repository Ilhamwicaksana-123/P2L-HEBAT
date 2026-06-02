<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ms_pesanan')
            ->where('status_pesanan', 'menunggu')
            ->update(['status_pesanan' => 'menunggu_pembayaran']);

        DB::table('ms_pesanan')
            ->where('status_pesanan', 'menunggu_verifikasi')
            ->update(['status_pesanan' => 'diproses']);
    }

    public function down(): void
    {
        DB::table('ms_pesanan')
            ->where('status_pesanan', 'menunggu_pembayaran')
            ->update(['status_pesanan' => 'menunggu']);

        DB::table('ms_pesanan')
            ->where('status_pesanan', 'diproses')
            ->update(['status_pesanan' => 'menunggu_verifikasi']);
    }
};
