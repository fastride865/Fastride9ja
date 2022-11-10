<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictedAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restricted_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('country_area_id')->comment('Geofence Area');
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->tinyInteger('restrict_area')->nullable()->comment('1 - For Pickup, 2 - For Drop, 3 - Both');
            $table->tinyInteger('restrict_type')->nullable()->comment('1 - Allowed, 2 - Disallowed');
            $table->string('base_areas',191)->nullable()->comment('Base area for geofence');
//            $table->string('queue_management')->nullable();
            $table->tinyInteger('status')->nullable()->comment('1 - Active, 0 - Not Active');
            $table->tinyInteger('queue_system')->nullable()->comment('1 - Active, 0 - Not Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restricted_areas');
    }
}
