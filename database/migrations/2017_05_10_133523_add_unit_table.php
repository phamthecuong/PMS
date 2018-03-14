<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mstMethod_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code_id', 4);
            $table->string('code_name', 10);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::table('mstRepair_method', function (Blueprint $table) {
            $table->unsignedInteger('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mstMethod_unit');
        Schema::table('mstRepair_method', function (Blueprint $table) {
            $table->dropColumn('unit_id');
        });
    }
}
