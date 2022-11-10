<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedinteger('business_segment_id');
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedinteger('option_type_id');
            $table->foreign('option_type_id')->references('id')->on('option_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->tinyInteger('delete')->nullable();
            $table->integer('sequence')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
