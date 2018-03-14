<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectiondataTVTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSectiondata_TV', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id');
            $table->string('name_en');
            $table->string('name_vn');
            $table->integer('km_station');
            $table->integer('m_station');
            $table->decimal('lat_station', 10, 8)->nullable();
            $table->decimal('lng_station', 11, 8)->nullable();
            $table->date('survey_time');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('del_flg');
            $table->integer('sb_id');
            $table->integer('branch_id');
            $table->string('remark');
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
        Schema::dropIfExists('tblSectiondata_TV');
    }
}
