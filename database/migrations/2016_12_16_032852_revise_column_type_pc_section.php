<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReviseColumnTypePcSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblSection_PC', function (Blueprint $table) {
            $table->dropColumn('lane_position_no');
            $table->dropColumn('number_of_lane_U');
            $table->dropColumn('number_of_lane_D');
        });

        Schema::table('tblSection_PC', function (Blueprint $table) {
            $table->integer('km_from')->unsigned()->change();
            $table->integer('km_to')->unsigned()->change();
            $table->string('direction', 1)->comment('U|D')->nullable(false)->change();
            $table->text('note')->nullable()->change();
            $table->tinyInteger('lane_position_no')->length(3)->unsigned();
            $table->tinyInteger('number_of_lane_U')->length(3)->unsigned();
            $table->tinyInteger('number_of_lane_D')->length(3)->unsigned();
            $table->decimal('cracking_ratio_cracking', 8, 4)->change();
            $table->decimal('cracking_ratio_patching', 8, 4)->change();
            $table->decimal('cracking_ratio_pothole', 8, 4)->change();
            $table->decimal('cracking_ratio_total', 8, 4)->change();
            $table->decimal('rutting_depth_max', 8, 4)->change();
            $table->decimal('rutting_depth_ave', 8, 4)->change(); 
            $table->decimal('IRI', 8, 4)->change(); 
            $table->decimal('MCI', 8, 4)->change();
        });

        Schema::table('tblSection_PC_history', function (Blueprint $table) {
            $table->dropColumn('lane_position_no');
            $table->dropColumn('number_of_lane_U');
            $table->dropColumn('number_of_lane_D');
        });

        Schema::table('tblSection_PC_history', function (Blueprint $table) {
            $table->string('direction', 1)->comment('U|D')->nullable(false)->change();
            $table->tinyInteger('lane_position_no')->length(3)->unsigned();
            $table->tinyInteger('number_of_lane_U')->length(3)->unsigned();
            $table->tinyInteger('number_of_lane_D')->length(3)->unsigned();
            $table->decimal('cracking_ratio_cracking', 8, 4)->change();
            $table->decimal('cracking_ratio_patching', 8, 4)->change();
            $table->decimal('cracking_ratio_pothole', 8, 4)->change();
            $table->decimal('cracking_ratio_total', 8, 4)->change();
            $table->decimal('rutting_depth_max', 8, 4)->change();
            $table->decimal('rutting_depth_ave', 8, 4)->change(); 
            $table->decimal('IRI', 8, 4)->change(); 
            $table->decimal('MCI', 8, 4)->change();
            $table->text('note')->nullable()->change();
        });
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
