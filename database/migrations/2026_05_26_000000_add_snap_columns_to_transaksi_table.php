<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tr_transaksi')) {
            return;
        }

        Schema::table('tr_transaksi', function (Blueprint $table) {
            if (! Schema::hasColumn('tr_transaksi', 'snap_token')) {
                $table->string('snap_token', 255)->nullable()->after('token_pembayaran');
            }

            if (! Schema::hasColumn('tr_transaksi', 'snap_redirect_url')) {
                $table->string('snap_redirect_url', 255)->nullable()->after('snap_token');
            }

            if (! Schema::hasColumn('tr_transaksi', 'payment_type')) {
                $table->string('payment_type', 50)->nullable()->after('status_pembayaran');
            }

            if (! Schema::hasColumn('tr_transaksi', 'payment_response')) {
                $table->json('payment_response')->nullable()->after('payment_type');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tr_transaksi')) {
            return;
        }

        Schema::table('tr_transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('tr_transaksi', 'payment_response')) {
                $table->dropColumn('payment_response');
            }

            if (Schema::hasColumn('tr_transaksi', 'payment_type')) {
                $table->dropColumn('payment_type');
            }

            if (Schema::hasColumn('tr_transaksi', 'snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }

            if (Schema::hasColumn('tr_transaksi', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
        });
    }
};
