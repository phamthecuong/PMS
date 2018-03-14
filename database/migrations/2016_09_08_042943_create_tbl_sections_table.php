<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblSection', function (Blueprint $table) {
            $table->increments('id');
			$table->string('section_code', 45);
			$table->dateTime('created');
			$table->integer('SB_id');
			$table->integer('branch_id');
			$table->string('m_from', 5);
			$table->string('km_from',4);
			$table->string('branch_number',2);
			$table->string('route_number',3);
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
        Schema::dropIfExists('tblSection');
    }
}
