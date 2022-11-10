<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('advertisement_banners', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('segment_id')->nullable();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('business_segment_id')->unsigned()->nullable();
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('name',191)->nullable();
            $table->text('image')->nullable();
            $table->text('redirect_url')->nullable();
            $table->tinyInteger('validity')->comment('1 - unlimited, 2 - limited')->nullable();
            $table->tinyInteger('home_screen')->comment('1 - Yes, 2 - No')->default(2);
            $table->date('activate_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->integer('sequence')->nullable();
            $table->integer('status')->nullable();
            $table->integer('is_deleted')->nullable();
            $table->string('banner_for')->comment('1 - User, 2 - Driver, 3 - Restaurant, 4 - All');
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
        Schema::dropIfExists('advertisement_banners');
    }
}
