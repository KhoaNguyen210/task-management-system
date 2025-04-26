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
            if (!Schema::hasTable('departments')) {
                Schema::create('departments', function (Blueprint $table) {
                    $table->id('department_id');
                    $table->string('name');
                    // Khóa ngoại đến user_id của trưởng bộ môn, cho phép null
                    $table->unsignedBigInteger('head_id')->nullable();
                    // Cột created_at và updated_at
                    $table->timestamps();

                    $table->engine = 'InnoDB';
                    $table->charset = 'utf8mb4';
                    $table->collation = 'utf8mb4_unicode_ci';
                    //Khóa ngoại cho head_id sẽ được thêm ở migration sau
                });
            }
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('departments');
        }
    };
    