<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandymanOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handyman_order_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('handyman_order_id');
            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('service_type_id')->nullable();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('segment_price_card_detail_id')->nullable();
            $table->foreign('segment_price_card_detail_id')->references('id')->on('segment_price_card_details')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('quantity')->nullable();
            $table->decimal('price',10,2)->nullable();
            $table->decimal('discount',10,2)->nullable();
            $table->decimal('total_amount',10,2)->nullable();
            $table->tinyInteger('status');
//            $table->text('booking_images')->nullable();
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
        Schema::dropIfExists('handyman_order_details');
    }
}
