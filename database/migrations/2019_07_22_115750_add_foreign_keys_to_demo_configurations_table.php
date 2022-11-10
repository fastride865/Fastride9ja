<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDemoConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('demo_configurations', function(Blueprint $table)
		{
            $table->integer('merchant_id')->unsigned()->change();
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned()->change();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_type_id')->unsigned()->change();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_make_id')->unsigned()->change();
            $table->foreign('vehicle_make_id')->references('id')->on('vehicle_makes')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_model_id')->unsigned()->change();
            $table->foreign('vehicle_model_id')->references('id')->on('vehicle_models')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('demo_configurations', function(Blueprint $table)
		{
            $table->dropForeign('merchant_id');
            $table->dropForeign('country_area_id');
            $table->dropForeign('vehicle_make_id');
            $table->dropForeign('vehicle_type_id');
            $table->dropForeign('vehicle_model_id');
		});
	}

}
