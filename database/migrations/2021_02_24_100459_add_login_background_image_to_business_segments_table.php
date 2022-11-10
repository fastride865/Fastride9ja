<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginBackgroundImageToBusinessSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_segments', function (Blueprint $table) {
            // column is duplicate
//            $table->string('login_background_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_segments', function (Blueprint $table) {
            //
//             $table->dropColumn('login_background_image');
        });
    }
}
