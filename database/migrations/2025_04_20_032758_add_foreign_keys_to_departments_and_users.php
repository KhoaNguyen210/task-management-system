<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Migration để thêm các khóa ngoại sau khi các bảng chính đã được tạo
return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm khóa ngoại cho head_id (departments) và department_id (users).
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                // Chỉ thêm khóa ngoại nếu cột head_id tồn tại và chưa có khóa ngoại
                if (Schema::hasColumn('departments', 'head_id') && !$this->hasForeignKey('departments', 'departments_head_id_foreign')) {
                    $table->foreign('head_id')
                          ->references('user_id')->on('users')
                          ->onDelete('set null'); // Nếu user bị xóa, đặt head_id là NULL
                }
            });

            Schema::table('users', function (Blueprint $table) {
                 // Chỉ thêm khóa ngoại nếu cột department_id tồn tại và chưa có khóa ngoại
                 if (Schema::hasColumn('users', 'department_id') && !$this->hasForeignKey('users', 'users_department_id_foreign')) {
                    $table->foreign('department_id')
                          ->references('department_id')->on('departments')
                          ->onDelete('set null'); // Nếu department bị xóa, đặt department_id là NULL
                 }
            });
        } else {
             Log::warning('Skipping foreign key creation in add_foreign_keys migration because prerequisite tables do not exist.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chỉ xóa khóa ngoại nếu bảng tồn tại
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                 if ($this->hasForeignKey('departments', 'departments_head_id_foreign')) {
                    $table->dropForeign(['head_id']);
                 }
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->hasForeignKey('users', 'users_department_id_foreign')) {
                    $table->dropForeign(['department_id']);
                }
            });
        }
    }

    /**
     * Helper function to check if a foreign key exists using Doctrine DBAL.
     * Cần cài đặt `doctrine/dbal`: composer require doctrine/dbal
     * @param string $table Tên bảng
     * @param string $foreignKeyName Tên khóa ngoại cần kiểm tra
     * @return bool
     */
    protected function hasForeignKey(string $table, string $foreignKeyName): bool
    {
        try {
            if (!interface_exists(\Doctrine\DBAL\Driver::class)) {
                 Log::warning('doctrine/dbal is required to reliably check foreign key existence. Assuming key does not exist.');
                 return false;
            }
            $connection = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $connection->listTableForeignKeys($table);

            foreach ($foreignKeys as $foreignKey) {
                if (strtolower($foreignKey->getName()) === strtolower($foreignKeyName)) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error checking foreign key existence for table '{$table}': " . $e->getMessage());
            return false;
        }
        return false;
    }
};