<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluationFieldsToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('evaluation_level')->nullable()->after('status');
            $table->text('evaluation_comment')->nullable()->after('evaluation_level');
            $table->unsignedBigInteger('evaluated_by')->nullable()->after('evaluation_comment');
            $table->foreign('evaluated_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluation_level', 'evaluation_comment', 'evaluated_by']);
        });
    }
}