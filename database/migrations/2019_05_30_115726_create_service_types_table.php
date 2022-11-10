<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_types', function(Blueprint $table)
		{
			$table->increments('id');

      $table->integer('parent_id')->unsigned()->nullable();

      $table->integer('segment_id')->unsigned()->index('segment_id_foreign');
      $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

      $table->string('serviceName', 191);
			$table->unique(['segment_id','serviceName'],'service_types_servicename_unique');
			$table->integer('serviceStatus');
//			$table->integer('package')->nullable()->default(0);
			$table->integer('type')->default(0)->comment('type means normal, rental, outstation etc ');
			$table->integer('additional_support')->nullable()->comment('means service will support packages, outstation city etc');
			$table->tinyInteger('owner')->default(1)->comment("1:super-admin, 2:admin/merchant");

			$table->integer('owner_id')->unsigned()->nullable();
			$table->foreign('owner_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::drop('service_types');
	}

}
