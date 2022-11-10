<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->double('reward_points')->nullable();
    			$table->double('usable_reward_points')->nullable();
    			$table->unsignedInteger('use_reward_trip_count')->default(0);
    			$table->unsignedTinyInteger('first_reward_pending')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn(['reward_points','usable_reward_points' , 'use_reward_trip_count','first_reward_pending']);
        });
    }
}
