<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMstMaterialType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tblMaterial_type', 'mstPavement_type');
        Schema::table('mstPavement_type', function (Blueprint $table) {
            $table->dropColumn('dropdown');
            $table->integer('pavement_layer_id')->unsigned()->nullable();
            $table->foreign('pavement_layer_id')->references('id')->on('mstPavement_layer')->onDelete('restrict')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Material_type', function (Blueprint $table) {
            //
        });
    }
}
