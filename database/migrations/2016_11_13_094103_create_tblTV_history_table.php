<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTVHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblTV_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sectiondata_id');
            $table->integer('segment_id')->default(0);
            $table->string('name_en');
            $table->string('name_vn');
            $table->integer('km_station');
            $table->integer('m_station');
            $table->decimal('lat_station', 10, 8)->nullable();
            $table->decimal('lng_station', 11, 8)->nullable();
            $table->date('survey_time')->comment('Year count traffic');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('del_flg');
            $table->integer('sb_id');
            $table->integer('branch_id');
            $table->string('remark');
            $table->enum('status', ['insert', 'delete','update']);
            $table->datetime('effect_at');
            $table->datetime('nullity_at');
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
        Schema::dropIfExists('tblTV_history');
    }
}
