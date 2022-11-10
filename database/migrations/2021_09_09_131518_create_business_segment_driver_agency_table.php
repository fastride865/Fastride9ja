<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessSegmentDriverAgencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('business_segment_driver_agency')) {
            Schema::create('business_segment_driver_agency', function (Blueprint $table) {
                $table->unsignedInteger('business_segment_id');
                $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
                $table->unsignedInteger('driver_agency_id');
                $table->foreign('driver_agency_id')->references('id')->on('driver_agencies')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::dropIfExists('business_segment_driver_agency');
    }
}
