<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsTblSectiondataRMD extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_RMD', function (Blueprint $table) {
            $table->integer('pavement_type_id')->nullable();
            $table->decimal('pavement_thickness', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSectiondata_RMD', function (Blueprint $table) {
            //
        });
    }
}
