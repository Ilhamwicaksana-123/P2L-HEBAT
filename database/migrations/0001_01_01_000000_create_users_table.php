<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ms_user')) {
            Schema::create('ms_user', function (Blueprint $table) {
                $table->integer('id_user', true);
                $table->string('nama', 30);
                $table->string('email', 30)->unique('email_unique');
                $table->string('password', 60)->nullable();
                $table->string('google_id', 30)->nullable()->unique('google_id_unique');
                $table->string('no_hp', 15);
                $table->enum('role', ['super_admin', 'admin', 'user'])->default('user');
                $table->string('foto_profil', 191)->nullable();
                $table->enum('status', ['aktif', 'tidak aktif'])->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->integer('id_user')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('ms_user');
    }
};
