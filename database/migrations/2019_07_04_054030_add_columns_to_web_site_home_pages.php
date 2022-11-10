<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToWebSiteHomePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_site_home_pages', function (Blueprint $table) {
            $table->string('user_estimate_image')->nullable();
            $table->string('featured_component_main_image')->nullable();
            $table->string('user_login_bg_image')->nullable();
            $table->string('driver_login_bg_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_site_home_pages', function (Blueprint $table) {
            $table->dropColumn(['user_estimate_image' , 'user_login_bg_image' , 'driver_login_bg_image']);
            $table->dropColumn('featured_component_main_image');
        });
    }
}
