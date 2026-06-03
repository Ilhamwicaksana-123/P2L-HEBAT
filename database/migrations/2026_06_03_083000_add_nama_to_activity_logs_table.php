<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs') || Schema::hasColumn('activity_logs', 'nama')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('nama', 30)->nullable()->after('id_produk');
        });

        if (Schema::hasColumn('activity_logs', 'name')) {
            DB::table('activity_logs')
                ->whereNull('nama')
                ->update([
                    'nama' => DB::raw('name'),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_logs') || ! Schema::hasColumn('activity_logs', 'nama')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('nama');
        });
    }
};
