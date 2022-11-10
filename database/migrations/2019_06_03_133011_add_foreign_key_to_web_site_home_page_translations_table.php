<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToWebSiteHomePageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_site_home_page_translations', function (Blueprint $table) {
            //
            $table->integer('web_site_home_page_id')->unsigned()->nullable()->change();
            $table->foreign('web_site_home_page_id')->references('id')->on('web_site_home_pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_site_home_page_translations', function (Blueprint $table) {
            //
            $table->dropForeign('web_site_home_page_id');
        });
    }
}
