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
        Schema::table('hasil_tangkapan', function (Blueprint $table) {
            $table->string('foto_ikan')->nullable()->after('grade');
            $table->unsignedBigInteger('penawaran_id')->nullable()->after('nelayan_id');
            $table->foreign('penawaran_id')->references('id')->on('penawarans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tangkapan', function (Blueprint $table) {
            $table->dropForeign(['penawaran_id']);
            $table->dropColumn(['foto_ikan', 'penawaran_id']);
        });
    }
};
