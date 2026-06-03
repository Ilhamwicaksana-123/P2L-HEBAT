<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('id_user')->nullable()->index();
                $table->integer('id_produk')->nullable()->index();
                $table->string('nama', 30)->nullable();
                $table->string('role', 20)->nullable();
                $table->string('action', 50);
                $table->string('module', 50)->nullable();
                $table->text('description')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'id_produk')) {
                $table->integer('id_produk')->nullable()->index()->after('id_user');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_logs') || ! Schema::hasColumn('activity_logs', 'id_produk')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('id_produk');
        });
    }
};
