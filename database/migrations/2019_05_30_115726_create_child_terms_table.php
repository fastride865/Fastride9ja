<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChildTermsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('child_terms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('child_terms_merchant_id_foreign');
			$table->integer('country_id')->unsigned()->index('child_terms_country_id_foreign');
			$table->integer('application')->default(1)->comment('1: User 2: Driver');
			$table->string('slug', 191)->comment('child_terms');
			$table->integer('status')->default(1)->comment('1: Enable 0:Disable');
			$table->integer('admin_delete')->default(0)->comment('1: Delete 0:Not Deleted');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('child_terms');
	}

}
