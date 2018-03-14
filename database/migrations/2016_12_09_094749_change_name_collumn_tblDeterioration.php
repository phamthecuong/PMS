<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameCollumnTblDeterioration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblDeterioration', function ($table) {
            // $table->renameColumn('cd_id', 'id');
            $table->dropColumn('cd_id');
            $table->string('id', 36);
        });
        // Schema::table('tblDeterioration', function ($table) {
        //     $table->string('id', 36);
        // });
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
