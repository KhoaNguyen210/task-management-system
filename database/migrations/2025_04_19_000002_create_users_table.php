<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['Dean', 'Department Head', 'Lecturer', 'Secretary', 'Educational Staff']);
            $table->unsignedBigInteger('department_id');
            $table->timestamp('last_login_time')->nullable();
            $table->unsignedInteger('failed_login_attempts')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};