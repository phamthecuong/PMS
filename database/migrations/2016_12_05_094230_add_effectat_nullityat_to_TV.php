<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEffectatNullityatToTV extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblSectiondata_TV', function (Blueprint $table) {
            //
        });
    }
}
