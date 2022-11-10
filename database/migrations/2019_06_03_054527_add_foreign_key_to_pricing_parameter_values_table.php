<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToPricingParameterValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pricing_parameter_values', function (Blueprint $table) {
            //
            $table->integer('pricing_parameter_id')->unsigned()->nullable()->change();
            $table->foreign('pricing_parameter_id')->references('id')->on('pricing_parameters')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pricing_parameter_values', function (Blueprint $table) {
            //
            $table->dropForeign('pricing_parameter_id');
        });
    }
}
