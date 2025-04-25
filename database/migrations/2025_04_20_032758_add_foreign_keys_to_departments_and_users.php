<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreign('head_id')->references('user_id')->on('users')->onDelete('set null');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};