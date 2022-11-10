<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralSystemSegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_system_segment', function (Blueprint $table) {
            $table->unsignedInteger('referral_system_id');
            $table->foreign('referral_system_id')->references('id')->on('referral_systems')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('segment_id');
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_system_segment');
    }
}
