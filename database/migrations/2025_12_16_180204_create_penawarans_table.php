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
        Schema::create('penawarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penawaran')->unique();
            $table->unsignedBigInteger('nelayan_id');
            $table->unsignedBigInteger('jenis_ikan_id');
            $table->decimal('jumlah_kg', 10, 2);
            $table->integer('harga_per_kg');
            $table->string('kualitas')->default('standar');
            $table->text('lokasi_tangkapan')->nullable();
            $table->string('kedalaman')->nullable();
            $table->date('tanggal_tangkapan');
            $table->text('catatan')->nullable();
            $table->string('foto_ikan')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'canceled'])->default('draft');
            $table->text('alasan_reject')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('nelayan_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->foreign('jenis_ikan_id')->references('id')->on('master_jenis_ikan')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('pengguna')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawarans');
    }
};
