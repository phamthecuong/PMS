<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyChoicesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSegment_history', function (Blueprint $table) {
            $table->dropColumn('choices');
            $table->enum('status', ['insert', 'delete','update']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSegment_history', function (Blueprint $table) {
            //
        });
    }
}
