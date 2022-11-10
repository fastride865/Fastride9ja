<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToDriverAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $table->unsignedTinyInteger('instant_settlement')->default(0)->comment('0-no, 1-yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $table->dropColumn(['instant_settlement']);
        });
    }
}
