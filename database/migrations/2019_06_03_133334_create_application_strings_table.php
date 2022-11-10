<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationStringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('application_strings')){
            Schema::create('application_strings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('platform');
                $table->string('application');
                $table->string('string_group_name')->nullable(); // group taxi
//                $table->unsignedInteger('application_module_id');
//                $table->foreign('application_module_id')->references('id')->on('application_modules')->onDelete('cascade');
                $table->longText('string_key');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_strings');
    }
}
