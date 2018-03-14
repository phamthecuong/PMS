<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection_layer', function (Blueprint $table) {
            $table->increments('id');
			$table->float('thickness')->comment('Unit : cm');
			$table->string('description');
			$table->integer('material_type_id');
			$table->integer('sectiondata_id');
			$table->integer('type')->comment('sectiondata_MH-> type');
            $table->integer('layer_id');
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
        Schema::dropIfExists('tblSection_layer');
    }
}
