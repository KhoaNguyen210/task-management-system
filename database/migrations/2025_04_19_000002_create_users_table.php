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
             if (!Schema::hasTable('users')) {
                Schema::create('users', function (Blueprint $table) {
                    $table->id('user_id');
                    $table->string('username')->unique();
                    $table->string('password');
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->enum('role', ['Dean', 'Department Head', 'Lecturer', 'Secretary', 'Educational Staff']);
                    // Khóa ngoại đến department_id, cho phép null
                    $table->unsignedBigInteger('department_id')->nullable();
                    $table->timestamp('last_login_time')->nullable();
                    $table->unsignedInteger('failed_login_attempts')->default(0);
                    $table->boolean('is_locked')->default(false);
                    // Cột created_at và updated_at
                    $table->timestamps();
                    
                    $table->engine = 'InnoDB';
                    $table->charset = 'utf8mb4';
                    $table->collation = 'utf8mb4_unicode_ci';
                    //Khóa ngoại cho department_id sẽ được thêm ở migration sau
                });
             }
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('users');
        }
    };
    