<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblOrganization', function (Blueprint $table) {
            $table->increments('id');
			$table->string('number_text',100);
			$table->string('name_en',45);
			$table->string('name_vn',45);
			$table->integer('level')->default('1')->comment('1: highest, 2: RMB/PDOT, 3: SB');
			$table->integer('parent_id')->default('0');
			$table->string('headquater_en',100);
			$table->string('headquater_vn',100);
			$table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('tblOrganization');
    }
}
