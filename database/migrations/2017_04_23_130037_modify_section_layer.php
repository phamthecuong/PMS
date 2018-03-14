<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySectionLayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('tblSection_layer_history', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('section_layer_id', 'layer_id');
            $table->integer('sectiondata_history_id')->unsigned();
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
            $table->tinyInteger('type')->unsigned()->comment('1: rmd, 2: mh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSection_layer_history', function (Blueprint $table) {
            //
        });
    }
}
