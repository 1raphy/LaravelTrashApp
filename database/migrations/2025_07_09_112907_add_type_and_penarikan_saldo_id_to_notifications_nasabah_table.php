<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications_nasabah', function (Blueprint $table) {
            $table->string('type')->after('user_id'); // 'registrasi', 'setoran', 'penarikan'
            $table->foreignId('penarikan_saldo_id')->nullable()->after('setoran_sampah_id')->constrained('penarikan_saldo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications_nasabah', function (Blueprint $table) {
            $table->dropForeign(['penarikan_saldo_id']);
            $table->dropColumn(['type', 'penarikan_saldo_id']);
        });
    }
};
