<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTblPlannedSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblPlanned_sections', function (Blueprint $table) {
            $table->integer('unit_cost')->nullable();
            $table->boolean('import_flg')->default(0);
            $table->string('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblPlanned_sections', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
            $table->dropColumn('import_flg');
            $table->dropColumn('remark');
        });
    }
}
