<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDeterioration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblDeterioration', function (Blueprint $table) {
            $table->string('cd_id')->comment('UNIQUE. Sinh tự động, độ dài 36. UUID.');
            $table->integer('region_id');
            $table->integer('distress_type')->comment('1: Crack ratio, 2: rutting depth, 3: IRI');
            $table->string('year_of_dataset');
            $table->integer('created_by')->nullable();
            $table->text('condition_rank')->nullable()->comment('json for data of tblCondition_rank. Variable based on distress type.');
            $table->text('selected_rank')->nullable()->comment('e.g: [1,2,3]');
            $table->text('summary_table_data')->nullable();
            $table->integer('benchmark_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('pav_type_fi_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('pav_type_eps_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('route_21_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('route_22_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('section_31_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('section_32_flg')->default(0)->comment('0: pending, 1: complete');
            $table->integer('status')->default(0)->comment('0: pending, 1: complete. TRIGGER update');
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
        //
    }
}
