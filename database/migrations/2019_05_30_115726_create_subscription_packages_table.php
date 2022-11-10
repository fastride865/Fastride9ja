<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('subscription_packages_merchant_id_foreign');
			$table->integer('package_duration_id')->unsigned()->index('subscription_packages_package_duration_id_foreign');
			$table->integer('max_trip');
			$table->string('image', 191)->nullable();
			$table->decimal('price');
			$table->integer('status')->default(1);
			$table->integer('package_type')->default(2);
			$table->date('expire_date')->nullable();
			$table->integer('admin_delete')->nullable()->default(0)->comment('1:Deleted 0:Not Deleted');
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
		Schema::drop('subscription_packages');
	}

}
