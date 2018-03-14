<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPcTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        Schema::table('tblPC_point_history', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('PC_point_id');
            $table->renameColumn('section_id', 'section_history_id');
        });

        Schema::table('tblPC_point', function (Blueprint $table) {
            $table->dropColumn('transferred_flg');
        });

        Schema::table('tblSection_PC', function (Blueprint $table) {
            $table->smallInteger('PC_flg')->nullable()->change();
            $table->decimal('min_lat', 10, 8)->nullable();
            $table->decimal('max_lat', 10, 8)->nullable();
            $table->decimal('min_lng', 11, 8)->nullable();
            $table->decimal('max_lng', 11, 8)->nullable();
            $table->longText('points')->nullable();
        });

        Schema::table('tblSection_PC_history', function (Blueprint $table) {
            $table->smallInteger('PC_flg')->nullable()->change();
            $table->decimal('min_lat', 10, 8)->nullable();
            $table->decimal('max_lat', 10, 8)->nullable();
            $table->decimal('min_lng', 11, 8)->nullable();
            $table->decimal('max_lng', 11, 8)->nullable();
            $table->longText('points')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
