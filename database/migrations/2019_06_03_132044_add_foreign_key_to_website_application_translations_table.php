<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToWebsiteApplicationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_application_translations', function (Blueprint $table) {
            //
            $table->integer('website_application_feature_id')->unsigned()->nullable()->change();
            $table->foreign('website_application_feature_id', 'waf_id')->references('id')->on('website_application_features')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_application_translations', function (Blueprint $table) {
            //
            $table->dropForeign('website_application_feature_id');
        });
    }
}
