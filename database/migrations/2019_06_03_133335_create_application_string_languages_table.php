<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationStringLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('application_string_languages')){
            Schema::create('application_string_languages', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('application_string_id');
                $table->foreign('application_string_id')->references('id')->on('application_strings')->onDelete('cascade');
                $table->longText('string_value');
                $table->string('locale');
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
        Schema::dropIfExists('application_string_languages');
    }
}
