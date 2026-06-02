<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('ms_alamat')) {
            return;
        }

        $maxLength = DB::table('ms_alamat')->selectRaw('MAX(CHAR_LENGTH(alamat)) as max_length')->value('max_length');

        if ($maxLength !== null && (int) $maxLength > 70) {
            throw new \RuntimeException('Migrasi dibatalkan: ada data ms_alamat.alamat yang panjangnya melebihi 70 karakter.');
        }

        DB::statement('ALTER TABLE ms_alamat MODIFY alamat VARCHAR(70) NOT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('ms_alamat')) {
            return;
        }

        DB::statement('ALTER TABLE ms_alamat MODIFY alamat TEXT NOT NULL');
    }
};
