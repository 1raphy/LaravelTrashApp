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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'registrasi', 'setoran', 'penarikan'
            $table->string('message');
            $table->string('status'); // 'pending', 'disetujui', 'ditolak', 'selesai'
            $table->foreignId('setoran_sampah_id')->nullable()->constrained('setoran_sampah')->onDelete('cascade');
            $table->foreignId('penarikan_saldo_id')->nullable()->constrained('penarikan_saldo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
