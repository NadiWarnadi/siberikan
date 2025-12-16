<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('pembeli_id')->constrained('pengguna')->onDelete('cascade');
            $table->date('tanggal_retur');
            $table->enum('alasan', ['rusak', 'tidak_sesuai', 'kadaluarsa', 'lainnya']);
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah_pengembalian', 12, 2);
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'selesai'])->default('diajukan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_barang');
    }
};
