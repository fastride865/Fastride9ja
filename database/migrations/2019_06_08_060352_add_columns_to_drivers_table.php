<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
          $table->double('reward_points')->default(0);
          $table->double('usable_reward_points')->default(0);
          $table->unsignedInteger('use_reward_trip_count')->default(0);
          $table->timestamp('is_suspended')->nullable();
          $table->tinyInteger('rider_gender_choice')->after('driver_gender')->default(0)->comment('0 :All,1 :Male,2 :Female');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
          $table->dropColumn(['reward_points' , 'usable_reward_points' , 'use_reward_trip_count' , 'is_suspended']);
        });
    }
}
