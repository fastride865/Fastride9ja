<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverSegmentDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_segment_documents', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('segment_id')->unsigned();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('document_id')->unsigned();
            $table->foreign('document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('document_file', 191);
            $table->date('expire_date')->nullable();
            $table->tinyInteger('document_verification_status');
            $table->tinyInteger('status')->default(1)->comment('1:Document Active, 2: Document Removed from area');
            $table->string('document_number',191)->nullable();

            $table->integer('reject_reason_id')->unsigned()->nullable();
            $table->foreign('reject_reason_id')->references('id')->on('reject_reasons')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('temp_document_file', 191)->nullable();
            $table->date('temp_expire_date')->nullable();
            $table->tinyInteger('temp_doc_verification_status');

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
        Schema::dropIfExists('driver_segment_documents');
    }
}
