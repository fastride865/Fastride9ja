<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('sc_address_status' , 50)->nullable()->comment('pending, rejected, verified');
            $table->string('sc_identity_photo')->nullable()->comment('stripe connect identity photo');
            $table->string('sc_identity_photo_status' , 10)->nullable()->comment('pending , verified, rejected');
            $table->string('routing_number')->nullable()->after('account_number');
            $table->string('ssn')->nullable();
            $table->string('device_ip')->nullable()->comment('ip while registering');
            $table->string('sc_account_id')->nullable()->comment('stripe connect account id');
            $table->string('sc_account_status')->nullable()->comment('active, rejected, pending');
            $table->string('bsb_number')->nullable();
            $table->string('abn_number')->nullable();

            $table->string('paystack_account_id')->nullable()->comment('Paystack split account id');
            $table->string('paystack_account_status')->nullable()->comment('active, rejected, pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['ssn', 'device_ip', 'sc_account_id' , 'sc_account_status','sc_address_status','sc_identity_photo','sc_identity_photo_status','routing_number']);
        });
    }
}
