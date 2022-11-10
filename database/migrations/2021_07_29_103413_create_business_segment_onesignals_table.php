<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSegmentOnesignalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_segment_onesignals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('business_segment_id')->unsigned()->index('business_segment_configurations_business_segment_id_foreign');
            $table->string('application_key')->nullable();
            $table->string('rest_key')->nullable();
            $table->string('channel_id')->nullable();
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
        Schema::dropIfExists('business_segment_onesignals');
    }
}
