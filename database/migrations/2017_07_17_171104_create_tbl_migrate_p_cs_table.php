<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMigratePCsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblMigrate_PC', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->decimal('total_km', 20, 3)->unsigned();
            $table->text('pc_file');
            $table->text('image_file');
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
        Schema::dropIfExists('tblMigrate_PC');
    }
}
