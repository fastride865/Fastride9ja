<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
//            these columns are already added while created table
//            $table->string('reference_id')->nullable()->comment('Payment Gateway Reference Id')->change();
//            $table->string('status_message')->default('Pending');
//            $table->string('amount')->after('checkout_id')->comment('e.g. INR 50.00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
