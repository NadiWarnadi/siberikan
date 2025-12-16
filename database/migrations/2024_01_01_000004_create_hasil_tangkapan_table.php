<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_tangkapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nelayan_id')->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('jenis_ikan_id')->constrained('master_jenis_ikan')->onDelete('cascade');
            $table->decimal('berat', 10, 2); // kg
            $table->enum('grade', ['A', 'B', 'C']);
            $table->decimal('harga_per_kg', 10, 2);
            $table->date('tanggal_tangkap');
            $table->enum('status', ['tersedia', 'terjual', 'rusak'])->default('tersedia');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tangkapan');
    }
};
