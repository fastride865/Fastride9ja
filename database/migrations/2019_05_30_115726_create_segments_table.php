<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSegmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('segments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('icon', 50)->nullable();
			$table->string('name', 50)->nullable();
			$table->string('description')->nullable();
			$table->string('slag', 50)->nullable();

			$table->tinyInteger('sub_group_for_app')->nullable();
			$table->tinyInteger('sub_group_for_admin')->nullable();
//			$table->tinyInteger('is_coming_soon')->nullable()->default(2)->comment('1 Yes, 2 : No');
			$table->integer('segment_group_id')->unsigned();
            $table->foreign('segment_group_id')->references('id')->on('segment_groups')->onUpdate('RESTRICT')->onDelete('CASCADE');

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
		Schema::drop('segments');
	}

}
