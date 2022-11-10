<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFareNewColumnsToDriverAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $table->integer('total_trips_till_now')->default(0)->nullable()->after('total_trips');
            $table->string('fare_amount')->default(0.0)->nullable()->after('amount');
            $table->string('company_commission')->default(0.0)->nullable()->after('amount');
            $table->string('toll_amount')->default(0.0)->nullable()->after('amount');
            $table->string('tip_amount')->default(0.0)->nullable()->after('amount');
            $table->string('cancellation_charges')->default(0.0)->nullable()->after('amount');
            $table->string('referral_amount')->default(0.0)->nullable()->after('amount');
            $table->string('cash_payment_received')->default(0.0)->nullable()->after('amount');
            $table->string('trips_outstanding_sum')->default(0.0)->nullable()->after('amount');
        });
    }

    /**
     * ALTER TABLE `driver_accounts` CHANGE `amount` `amount` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'calculated_amount', CHANGE `settle_type` `settle_type` INT(11) NULL DEFAULT NULL COMMENT '1: Cash 2: NonCash', CHANGE `status` `status` INT(11) NOT NULL DEFAULT '1' COMMENT '1:Generated Only 2:Settled';
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $table->dropColumn('total_trips_till_now');
            $table->dropColumn('fare_amount');
            $table->dropColumn('company_commission');
            $table->dropColumn('toll_amount');
            $table->dropColumn('tip_amount');
            $table->dropColumn('cancellation_charges');
            $table->dropColumn('referral_amount');
            $table->dropColumn('cash_payment_received');
            $table->dropColumn('trips_outstanding_sum');
        });
    }
}
