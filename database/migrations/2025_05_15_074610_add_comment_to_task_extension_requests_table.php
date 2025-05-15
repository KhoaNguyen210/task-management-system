<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentToTaskExtensionRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('task_extension_requests', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('approved_by');
        });
    }

    public function down()
    {
        Schema::table('task_extension_requests', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
}