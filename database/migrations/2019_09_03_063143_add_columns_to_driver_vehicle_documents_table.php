<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriverVehicleDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_vehicle_documents', function (Blueprint $table) {
            $table->string('temp_document_file')->nullable()->after('document_verification_status');
            $table->date('temp_expire_date')->nullable()->after('temp_document_file');
            $table->integer('temp_doc_verification_status')->nullable()->after('temp_expire_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_vehicle_documents', function (Blueprint $table) {
            //
        });
    }
}
