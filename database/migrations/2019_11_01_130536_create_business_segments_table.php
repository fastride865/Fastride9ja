<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if (!Schema::hasTable('business_segments'))
        {
            Schema::create('business_segments', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('merchant_id')->unsigned();
                $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('country_id')->unsigned();
                $table->foreign('country_id')->references('id')->on('countries')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('country_area_id')->unsigned();
                $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('segment_id')->unsigned();
                $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('parent_id')->unsigned()->default(0);

                $table->string('full_name',50);
                $table->string('alias_name',50);
                $table->string('email', 50)->index();
                $table->string('password');
                $table->string('business_logo');
                $table->string('phone_number');
                $table->text('open_time', 5);
                $table->text('close_time', 5);
                $table->string('pin_code',10)->nullable();
                $table->string('state',10)->nullable();
                $table->string('city',10)->nullable();
                $table->string('address');
                $table->string('latitude',50);
                $table->string('longitude',50);
                $table->string('landmark',150);

                $table->tinyInteger('commission_type')->default(1)->comment('1:PrePaid, 2:PostPaid');
                $table->tinyInteger('commission_method')->default(2)->comment('1:Flat, 2:Percentage');

                $table->decimal('commission',8,2)->comment('commission value');

                $table->tinyInteger('order_request_receiver')->default(2)->comment('1:Driver, 2:Admin');

                $table->tinyInteger('status')->default(1)->comment('1:Active, 2:Inactive');
                $table->tinyInteger('is_popular')->default(1)->comment('1:Yes, 2:No');
                $table->integer('delivery_time')->comment('time in minutes');
                $table->string('minimum_amount')->nullable()->comment('this amount will be shown on home screen');
                $table->string('minimum_amount_for')->nullable()->comment('no of persons');

                $table->string('wallet_amount')->default(0)->nullable();


                $table->tinyInteger('login')->nullable()->comment('1:Yes, 2:No');
                $table->string('player_id')->nullable();
                $table->string('unique_number')->nullable();
                $table->string('access_token_id')->nullable();
                $table->integer('device')->nullable();

                $table->string('remember_token')->nullable();
                $table->decimal('rating',5,1)->nullable()->default(4.5);
                $table->string('login_background_image')->nullable();
                $table->text('bank_details')->nullable();
                $table->tinyInteger('delivery_service')->default(1)->comment('1:Merchant, 2:Own/Driver Agency');
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
        Schema::dropIfExists('business_segments');
    }
}
