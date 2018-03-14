<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection_survey', function (Blueprint $table) {
            $table->increments('id');
			$table->string('section_id',25);
			$table->string('survey_year',4)->comment('YYYY');
            $table->datetime('created');
			$table->string('section_survey_id',25)->comment('section_id + year');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('tblSection_survey');
    }
}
