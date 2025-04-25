<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kiểm tra xem bảng users đã tồn tại chưa
        if (Schema::hasTable('users')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('assigned_to');
                $table->date('deadline');
                $table->text('requirements')->nullable();
                $table->timestamps();

                // Đảm bảo bảng sử dụng engine InnoDB và collation phù hợp
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                // Tạo khóa ngoại
                $table->foreign('assigned_to')
                      ->references('user_id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};