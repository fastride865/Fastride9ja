<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFamilyMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('family_members', function(Blueprint $table)
		{
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->nullable();
			$table->string('name')->nullable();
			$table->string('phoneNumber')->nullable();
			$table->string('age', 25)->nullable();
			$table->string('dob', 100)->nullable()->comment('DateOfBirth');
			$table->string('gender', 100)->nullable();
			$table->string('notes')->nullable()->comment('extra notes');
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
		Schema::drop('family_members');
	}

}
