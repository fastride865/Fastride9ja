<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToWebsiteFeatureTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('website_feature_translations', function (Blueprint $table) {
            //
            $table->integer('website_feature_id')->unsigned()->nullable()->change();
            $table->foreign('website_feature_id')->references('id')->on('website_features')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('website_feature_translations', function (Blueprint $table) {
            //
            $table->dropForeign('website_feature_id');
        });
    }
}
