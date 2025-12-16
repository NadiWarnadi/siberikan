<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('tengkulak_id')->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('pembeli_id')->constrained('pengguna')->onDelete('cascade');
            $table->date('tanggal_transaksi');
            $table->decimal('total_harga', 12, 2);
            $table->enum('status', ['pending', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
