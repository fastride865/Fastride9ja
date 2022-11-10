<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadoTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercado_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token')->nullable();

            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('business_segment_id')->unsigned()->nullable();
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')
                ->onDelete('CASCADE');

            $table->tinyInteger('status')->default(1)->comment('1:Active');
            $table->integer('mercado_user_id')->nullable()->comment('mercado user id');
            $table->text('additional_param')->nullable()->comment('additional param');

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
        Schema::dropIfExists('mercado_tokens');
    }
}
