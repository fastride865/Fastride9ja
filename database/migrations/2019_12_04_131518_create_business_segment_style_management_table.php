<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessSegmentStyleManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('business_segment_style_management')) {
            Schema::create('business_segment_style_management', function (Blueprint $table) {
                $table->unsignedInteger('business_segment_id');
                $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
                $table->unsignedInteger('style_management_id');
                $table->foreign('style_management_id')->references('id')->on('style_managements')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::dropIfExists('business_segment_style_management');
    }
}
