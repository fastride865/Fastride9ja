<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessSegmentKeysToOnesignalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('onesignals', function (Blueprint $table) {
            //
            $table->string('business_segment_application_key')->nullable();
            $table->string('business_segment_rest_key')->nullable();
            $table->string('business_segment_channel_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onesignals', function (Blueprint $table) {
            //
            $table->dropColumn('business_segment_application_key');
            $table->dropColumn('business_segment_rest_key');
            $table->dropColumn('business_segment_channel_id');
        });
    }
}
