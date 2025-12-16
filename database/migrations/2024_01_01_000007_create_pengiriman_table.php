<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('sopir_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('nomor_resi')->unique();
            $table->date('tanggal_kirim');
            $table->date('tanggal_estimasi');
            $table->date('tanggal_diterima')->nullable();
            $table->text('alamat_tujuan');
            $table->enum('status', ['menunggu', 'dalam_perjalanan', 'terkirim', 'gagal'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
