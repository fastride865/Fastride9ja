<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->unsignedTinyInteger('driver_suspend_penalty_enable')->nullable();
            $table->unsignedTinyInteger('fare_table_based_referral_enable')->nullable();
            $table->unsignedTinyInteger('cancel_rate_table_based_cancel_charges_enable')->nullable();
            $table->unsignedTinyInteger('driver_wallet_withdraw_enable')->nullable();
            $table->unsignedTinyInteger('ride_later_cancel_in_cancel_hour_enable')->nullable();
            $table->tinyInteger('add_money_to_user_wallet_ride_end')->nullable();
            $table->tinyInteger('admin_alert_on_driver_reg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('driver_suspend_penalty_enable');
            $table->dropColumn(['ride_later_cancel_in_cancel_hour_enable','driver_wallet_withdraw_enable','cancel_rate_table_based_cancel_charges_enable', 'fare_table_based_referral_enable']);
        });
    }
}
