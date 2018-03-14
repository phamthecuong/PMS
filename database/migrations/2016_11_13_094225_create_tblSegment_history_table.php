<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSegmentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSegment_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch_id');
            $table->string('segname_en',100);
            $table->string('segname_vn',100);
            $table->integer('km_from');
            $table->integer('m_from');
            $table->integer('km_to');
            $table->integer('m_to');
            $table->integer('prfrom_id');
            $table->integer('prto_id');
            $table->integer('distfrom_id');
            $table->integer('distto_id');
            $table->integer('SB_id');
            $table->string('commune_from');
            $table->string('commune_to');
            $table->enum('choices', ['insert', 'delete','update']);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('tblSegment_history');
    }
}
