<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PricingParameterSegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pricing_parameter_segment'))
        {
            Schema::create('pricing_parameter_segment', function (Blueprint $table) {
                $table->integer('pricing_parameter_id')->unsigned();
                $table->foreign('pricing_parameter_id')->references('id')->on('pricing_parameters')->onUpdate('RESTRICT')->onDelete('CASCADE');
                $table->integer('segment_id')->unsigned()->index('segment_id_foreign');
                $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::dropIfExists('pricing_parameter_segment');
    }
}
