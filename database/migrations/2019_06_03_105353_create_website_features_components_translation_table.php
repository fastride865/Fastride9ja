<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsiteFeaturesComponentsTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_features_components_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_features_components_id')->unsigned()->nullable();
            $table->foreign('website_features_components_id', 'wfc_id_foreign')->references('id')->on('website_features_components')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('title');
            $table->longText('description');
            $table->string('locale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_features_components_translations');
    }
}
