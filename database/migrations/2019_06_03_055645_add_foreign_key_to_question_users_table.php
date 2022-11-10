<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToQuestionUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_users', function (Blueprint $table) {
            //
//            $table->integer('question_id')->unsigned()->nullable()->change();
//            $table->foreign('question_id')->references('id')->on('questions')->onUpdate('RESTRICT')->onDelete('CASCADE');
//
//            $table->integer('user_id')->unsigned()->nullable()->change();
//            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_users', function (Blueprint $table) {
            //
//            $table->dropForeign('question_id');
//            $table->dropForeign('user_id');
        });
    }
}
