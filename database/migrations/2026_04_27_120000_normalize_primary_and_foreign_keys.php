<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->normalizeMsProduk();
        $this->normalizeMsAlamat();
        $this->normalizeMsKeranjangItem();
        $this->normalizeMsPesanan();
        $this->normalizeMsPesananDetail();
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->dropForeignIfExists('ms_pesanan_detail', 'fk_ms_pesanan_detail_produk');
        $this->dropForeignIfExists('ms_pesanan_detail', 'fk_ms_pesanan_detail_pesanan');
        $this->dropForeignIfExists('ms_pesanan', 'fk_ms_pesanan_alamat');
        $this->dropForeignIfExists('ms_pesanan', 'fk_ms_pesanan_user');
        $this->dropForeignIfExists('ms_keranjang_item', 'fk_ms_keranjang_item_produk');
        $this->dropForeignIfExists('ms_keranjang_item', 'fk_ms_keranjang_item_user');
        $this->dropForeignIfExists('ms_alamat', 'fk_ms_alamat_user');
        $this->dropForeignIfExists('ms_produk', 'fk_ms_produk_kategori');

        if (Schema::hasTable('ms_produk') && $this->hasColumns('ms_produk', ['id_produk', 'ms_kategori_id_kategori'])) {
            $primaryColumns = $this->primaryKeyColumns('ms_produk');

            if ($primaryColumns !== ['id_produk', 'ms_kategori_id_kategori']) {
                DB::statement('ALTER TABLE ms_produk DROP PRIMARY KEY, ADD PRIMARY KEY (id_produk, ms_kategori_id_kategori)');
                DB::statement('ALTER TABLE ms_produk MODIFY id_produk INT(11) NOT NULL AUTO_INCREMENT');
            }

            $this->createIndexIfMissing('ms_produk', 'fk_ms_produk_ms_kategori1_idx', 'ms_kategori_id_kategori');
            $this->createForeignIfMissing(
                'ms_produk',
                'fk_ms_produk_ms_kategori1',
                'ms_kategori_id_kategori',
                'ms_kategori',
                'id_kategori',
                'NO ACTION',
                'NO ACTION'
            );
        }

        if (Schema::hasTable('ms_alamat') && $this->hasColumns('ms_alamat', ['id_alamat', 'id_user'])) {
            $primaryColumns = $this->primaryKeyColumns('ms_alamat');

            if ($primaryColumns !== ['id_alamat', 'id_user']) {
                DB::statement('ALTER TABLE ms_alamat DROP PRIMARY KEY, ADD PRIMARY KEY (id_alamat, id_user)');
                DB::statement('ALTER TABLE ms_alamat MODIFY id_alamat INT(11) NOT NULL AUTO_INCREMENT');
            }

            $this->createIndexIfMissing('ms_alamat', 'fk_ms_alamat_ms_user_idx', 'id_user');
            $this->createForeignIfMissing(
                'ms_alamat',
                'fk_ms_alamat_ms_user',
                'id_user',
                'ms_user',
                'id_user',
                'NO ACTION',
                'NO ACTION'
            );
        }
    }

    private function normalizeMsProduk(): void
    {
        if (! Schema::hasTable('ms_produk') || ! $this->hasColumns('ms_produk', ['id_produk', 'ms_kategori_id_kategori'])) {
            return;
        }

        $this->dropForeignIfExists('ms_produk', 'fk_ms_produk_ms_kategori1');
        $this->dropForeignIfExists('ms_produk', 'fk_ms_produk_kategori');

        $primaryColumns = $this->primaryKeyColumns('ms_produk');

        if ($primaryColumns !== ['id_produk']) {
            DB::statement('ALTER TABLE ms_produk DROP PRIMARY KEY, ADD PRIMARY KEY (id_produk)');
        }

        DB::statement('ALTER TABLE ms_produk MODIFY id_produk INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE ms_produk MODIFY ms_kategori_id_kategori INT(11) NOT NULL');
        $this->createIndexIfMissing('ms_produk', 'idx_ms_produk_kategori', 'ms_kategori_id_kategori');
        $this->createForeignIfMissing(
            'ms_produk',
            'fk_ms_produk_kategori',
            'ms_kategori_id_kategori',
            'ms_kategori',
            'id_kategori',
            'RESTRICT',
            'CASCADE'
        );
    }

    private function normalizeMsAlamat(): void
    {
        if (! Schema::hasTable('ms_alamat') || ! Schema::hasTable('ms_user') || ! $this->hasColumns('ms_alamat', ['id_alamat', 'id_user'])) {
            return;
        }

        $this->dropForeignIfExists('ms_alamat', 'fk_ms_alamat_ms_user');
        $this->dropForeignIfExists('ms_alamat', 'fk_ms_alamat_user');

        $primaryColumns = $this->primaryKeyColumns('ms_alamat');

        if ($primaryColumns !== ['id_alamat']) {
            DB::statement('ALTER TABLE ms_alamat DROP PRIMARY KEY, ADD PRIMARY KEY (id_alamat)');
        }

        DB::statement('ALTER TABLE ms_alamat MODIFY id_alamat INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE ms_alamat MODIFY id_user INT(11) NOT NULL');
        $this->createIndexIfMissing('ms_alamat', 'idx_ms_alamat_user', 'id_user');
        $this->createForeignIfMissing(
            'ms_alamat',
            'fk_ms_alamat_user',
            'id_user',
            'ms_user',
            'id_user',
            'CASCADE',
            'CASCADE'
        );
    }

    private function normalizeMsKeranjangItem(): void
    {
        if (! Schema::hasTable('ms_keranjang_item')
            || ! Schema::hasTable('ms_user')
            || ! Schema::hasTable('ms_produk')
            || ! $this->hasColumns('ms_keranjang_item', ['ms_user_id_user', 'ms_produk_id_produk'])) {
            return;
        }

        $this->dropForeignIfExists('ms_keranjang_item', 'fk_ms_keranjang_item_user');
        $this->dropForeignIfExists('ms_keranjang_item', 'fk_ms_keranjang_item_produk');

        DB::statement('ALTER TABLE ms_keranjang_item MODIFY id_keranjang_item INT(11) NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE ms_keranjang_item MODIFY ms_user_id_user INT(11) NOT NULL');
        DB::statement('ALTER TABLE ms_keranjang_item MODIFY ms_produk_id_produk INT(11) NOT NULL');
        DB::statement('DELETE FROM ms_keranjang_item WHERE ms_user_id_user NOT IN (SELECT id_user FROM ms_user)');
        DB::statement('DELETE FROM ms_keranjang_item WHERE ms_produk_id_produk NOT IN (SELECT id_produk FROM ms_produk)');

        $this->createIndexIfMissing('ms_keranjang_item', 'idx_ms_keranjang_item_user', 'ms_user_id_user');
        $this->createIndexIfMissing('ms_keranjang_item', 'idx_ms_keranjang_item_produk', 'ms_produk_id_produk');

        $this->createForeignIfMissing(
            'ms_keranjang_item',
            'fk_ms_keranjang_item_user',
            'ms_user_id_user',
            'ms_user',
            'id_user',
            'CASCADE',
            'CASCADE'
        );

        $this->createForeignIfMissing(
            'ms_keranjang_item',
            'fk_ms_keranjang_item_produk',
            'ms_produk_id_produk',
            'ms_produk',
            'id_produk',
            'CASCADE',
            'CASCADE'
        );
    }

    private function normalizeMsPesanan(): void
    {
        if (! Schema::hasTable('ms_pesanan') || ! $this->hasColumns('ms_pesanan', ['ms_user_id_user', 'ms_alamat_id_alamat'])) {
            return;
        }

        $this->dropForeignIfExists('ms_pesanan', 'fk_ms_pesanan_user');
        $this->dropForeignIfExists('ms_pesanan', 'fk_ms_pesanan_alamat');

        DB::statement('ALTER TABLE ms_pesanan MODIFY id_pesanan INT(11) NOT NULL AUTO_INCREMENT');

        if (Schema::hasTable('ms_user')) {
            DB::statement('ALTER TABLE ms_pesanan MODIFY ms_user_id_user INT(11) NOT NULL');
            DB::statement('DELETE FROM ms_pesanan WHERE ms_user_id_user NOT IN (SELECT id_user FROM ms_user)');
            $this->createIndexIfMissing('ms_pesanan', 'idx_ms_pesanan_user', 'ms_user_id_user');
            $this->createForeignIfMissing(
                'ms_pesanan',
                'fk_ms_pesanan_user',
                'ms_user_id_user',
                'ms_user',
                'id_user',
                'RESTRICT',
                'CASCADE'
            );
        }

        if (Schema::hasTable('ms_alamat')) {
            DB::statement('ALTER TABLE ms_pesanan MODIFY ms_alamat_id_alamat INT(11) NULL');
            DB::statement('UPDATE ms_pesanan SET ms_alamat_id_alamat = NULL WHERE ms_alamat_id_alamat IS NOT NULL AND ms_alamat_id_alamat NOT IN (SELECT id_alamat FROM ms_alamat)');
            $this->createIndexIfMissing('ms_pesanan', 'idx_ms_pesanan_alamat', 'ms_alamat_id_alamat');
            $this->createForeignIfMissing(
                'ms_pesanan',
                'fk_ms_pesanan_alamat',
                'ms_alamat_id_alamat',
                'ms_alamat',
                'id_alamat',
                'SET NULL',
                'CASCADE'
            );
        }
    }

    private function normalizeMsPesananDetail(): void
    {
        if (! Schema::hasTable('ms_pesanan_detail') || ! $this->hasColumns('ms_pesanan_detail', ['ms_pesanan_id_pesanan', 'ms_produk_id_produk'])) {
            return;
        }

        $this->dropForeignIfExists('ms_pesanan_detail', 'fk_ms_pesanan_detail_pesanan');
        $this->dropForeignIfExists('ms_pesanan_detail', 'fk_ms_pesanan_detail_produk');

        if (Schema::hasTable('ms_pesanan')) {
            DB::statement('ALTER TABLE ms_pesanan_detail MODIFY id_pesanan_detail INT(11) NOT NULL AUTO_INCREMENT');
            DB::statement('ALTER TABLE ms_pesanan_detail MODIFY ms_pesanan_id_pesanan INT(11) NOT NULL');
            DB::statement('DELETE FROM ms_pesanan_detail WHERE ms_pesanan_id_pesanan NOT IN (SELECT id_pesanan FROM ms_pesanan)');
            $this->createIndexIfMissing('ms_pesanan_detail', 'idx_ms_pesanan_detail_pesanan', 'ms_pesanan_id_pesanan');
            $this->createForeignIfMissing(
                'ms_pesanan_detail',
                'fk_ms_pesanan_detail_pesanan',
                'ms_pesanan_id_pesanan',
                'ms_pesanan',
                'id_pesanan',
                'CASCADE',
                'CASCADE'
            );
        }

        if (Schema::hasTable('ms_produk')) {
            DB::statement('ALTER TABLE ms_pesanan_detail MODIFY ms_produk_id_produk INT(11) NOT NULL');
            DB::statement('DELETE FROM ms_pesanan_detail WHERE ms_produk_id_produk NOT IN (SELECT id_produk FROM ms_produk)');
            $this->createIndexIfMissing('ms_pesanan_detail', 'idx_ms_pesanan_detail_produk', 'ms_produk_id_produk');
            $this->createForeignIfMissing(
                'ms_pesanan_detail',
                'fk_ms_pesanan_detail_produk',
                'ms_produk_id_produk',
                'ms_produk',
                'id_produk',
                'RESTRICT',
                'CASCADE'
            );
        }
    }

    private function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function primaryKeyColumns(string $table): array
    {
        $rows = DB::select(
            "SELECT COLUMN_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = 'PRIMARY'
             ORDER BY ORDINAL_POSITION",
            [$table]
        );

        return array_map(static fn ($row) => $row->COLUMN_NAME, $rows);
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        if (Schema::hasTable($table) && $this->foreignKeyExists($table, $constraint)) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }

    private function createIndexIfMissing(string $table, string $index, string $column): void
    {
        if (! $this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE {$table} ADD INDEX {$index} ({$column})");
        }
    }

    private function createForeignIfMissing(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete,
        string $onUpdate
    ): void {
        if ($this->foreignKeyExists($table, $constraint)) {
            return;
        }

        DB::statement(
            "ALTER TABLE {$table}
             ADD CONSTRAINT {$constraint}
             FOREIGN KEY ({$column})
             REFERENCES {$referencedTable} ({$referencedColumn})
             ON DELETE {$onDelete}
             ON UPDATE {$onUpdate}"
        );
    }
};
