<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEffectNulltityToSegment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSegment', function (Blueprint $table) {
            $table->timestamp('effect_at')->nullable();
            $table->timestamp('nullity_at')->nullable();
        });

        Schema::table('tblSegment_history', function (Blueprint $table) {
            $table->dropColumn(array('effect_at', 'nullity_at'));
        });

        Schema::table('tblSegment_history', function (Blueprint $table) {
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
        Schema::table('tblSegment', function (Blueprint $table) {
            //
        });
    }
}
