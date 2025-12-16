<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengguna_id')->nullable();
            $table->enum('tipe_event', [
                'login_success',
                'login_failed',
                'login_brute_force',
                'unauthorized_access',
                'file_upload',
                'sql_injection_attempt',
                'xss_attempt',
                'csrf_violation',
                'password_change',
                'permission_denied',
                'invalid_input',
                'logout',
            ]);
            $table->text('deskripsi');
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('status_code')->nullable();
            $table->string('request_url')->nullable();
            $table->timestamps();

            $table->foreign('pengguna_id')
                ->references('id')
                ->on('pengguna')
                ->onDelete('set null');

            $table->index('tipe_event');
            $table->index('created_at');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
