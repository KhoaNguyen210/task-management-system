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
        if (!Schema::hasTable('tasks')) {
             // Đảm bảo các bảng tham chiếu tồn tại trước khi thêm khóa ngoại
             if (!Schema::hasTable('users') || !Schema::hasTable('departments')) {
                  throw new \Exception('Prerequisite tables (users, departments) do not exist for creating tasks table with foreign keys.');
             }

            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->date('due_date');
                $table->string('status')->default('Not Started');

                // Khóa ngoại đến người tạo (user_id trong bảng users)
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')
                      ->references('user_id')->on('users')
                      ->onDelete('set null'); // Nếu user tạo bị xóa, đặt là NULL

                // Khóa ngoại đến bộ môn (department_id trong bảng departments)
                $table->unsignedBigInteger('department_id')->nullable();
                $table->foreign('department_id')
                      ->references('department_id')->on('departments')
                      ->onDelete('set null'); // Nếu bộ môn bị xóa, đặt là NULL

                // Cột created_at và updated_at
                $table->timestamps();

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
        Schema::dropIfExists('tasks');
    }
};
