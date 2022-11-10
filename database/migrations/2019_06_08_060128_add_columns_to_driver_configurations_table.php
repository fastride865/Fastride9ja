<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriverConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_configurations', function (Blueprint $table) {
          $table->unsignedTinyInteger('driver_penalty_enable')->nullable();
    			$table->unsignedInteger('driver_cancel_count')->nullable();
    			$table->unsignedInteger('driver_penalty_period')->nullable();
    			$table->unsignedInteger('driver_penalty_period_next')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_configurations', function (Blueprint $table) {
          $table->dropColumn('driver_penalty_enable');
    			$table->dropColumn('driver_cancel_count');
    			$table->dropColumn('driver_penalty_period');
    			$table->dropColumn('driver_penalty_period_next');
        });
    }
}
