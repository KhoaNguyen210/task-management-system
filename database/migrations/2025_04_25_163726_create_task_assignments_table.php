<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration để tạo bảng trung gian task_assignments
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('task_assignments')) {
             if (!Schema::hasTable('tasks') || !Schema::hasTable('users')) {
                  throw new \Exception('Prerequisite tables (tasks, users) do not exist for creating task_assignments table with foreign keys.');
             }

            Schema::create('task_assignments', function (Blueprint $table) {
                $table->id('assignment_id');
                $table->unsignedBigInteger('task_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                // Định nghĩa khóa ngoại và hành động khi xóa
                $table->foreign('task_id')
                      ->references('id')->on('tasks') // Tham chiếu đến 'id' của tasks
                      ->onDelete('cascade'); // Nếu xóa task thì xóa luôn assignment
                $table->foreign('user_id')
                      ->references('user_id')->on('users') // Tham chiếu đến 'user_id' của users
                      ->onDelete('cascade'); // Nếu xóa user thì xóa luôn assignment

                // Đảm bảo mỗi cặp (task_id, user_id) là duy nhất
                $table->unique(['task_id', 'user_id']);

                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};