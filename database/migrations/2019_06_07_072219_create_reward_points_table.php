<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('reward_points')) {
            Schema::create('reward_points', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('CASCADE');
                $table->unsignedInteger('country_area_id');
                $table->foreign('country_area_id')->references('id')->on('country_areas')->onDelete('CASCADE');
                $table->unsignedTinyInteger('registration_enable');
                $table->double('user_registration_reward');
                $table->double('driver_registration_reward');
                $table->unsignedTinyInteger('referral_enable');
                $table->double('user_referral_reward');
                $table->double('driver_referral_reward');
                $table->double('value_equals');
                $table->double('max_redeem');
                $table->unsignedInteger('trips_count');
                $table->unsignedTinyInteger('active')->default();

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
        Schema::dropIfExists('reward_points');
    }
}
