<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantStripeConnectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_stripe_connects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->update('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('personal_document_id')->nullable()->comment('Stripe Connect Default Identity Document');
            $table->foreign('personal_document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('photo_front_document_id')->nullable()->comment('Stripe Connect Default Identity Document');
            $table->foreign('photo_front_document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('photo_back_document_id')->nullable()->comment('Stripe Connect Default Identity Document');
            $table->foreign('photo_back_document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('additional_document_id')->nullable()->comment('Stripe Connect Default Identity Document');
            $table->foreign('additional_document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->text('business_website');
            $table->string('email');
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
        Schema::dropIfExists('merchant_stripe_connects');
    }
}
