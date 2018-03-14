<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmsDatasetInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tblPMS_dataset_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('PMS_Dataset_id')->unsigned();
            $table->string('branch_number', 4)->nullable();
            $table->string('latest_condition_year', 4)->nullable();
            $table->string('latest_condition_month', 2)->nullable();
            $table->string('latest_pavement_type')->nullable();
            $table->string('latest2_condition_year', 4)->nullable();
            $table->string('latest2_condition_month', 2)->nullable();
            $table->string('latest2_pavement_type')->nullable();
            $table->string('pavement_type_code', 10)->nullable();
            $table->float('pavement_thickness')->nullable();
            $table->float('pavement_width')->nullable();
            $table->integer('segment_id')->unsigned()->nullable();
            $table->string('segment_en')->nullable();
            $table->string('segment_vn')->nullable();
            $table->string('service_start_year', 4)->nullable();
            $table->string('service_start_month', 2)->nullable();
            $table->string('construct_year', 4)->nullable();
            $table->string('construct_month', 2)->nullable();
            $table->float('annual_precipitation')->nullable();
            $table->float('temperature')->nullable();
            $table->integer('terrain_type_id')->nullable();
            $table->integer('road_class_id')->nullable();
            $table->string('structure_type_code', 50)->nullable();
            $table->string('crossing_type_code', 50)->nullable();
            $table->string('geographical_area', 50)->nullable();
            $table->float('analysis_area')->nullable();
            $table->tinyInteger('number_of_lane')->nullable();
            $table->float('latest_cracking')->nullable();
            $table->float('latest_patching')->nullable();
            $table->float('latest_pothole')->nullable();
            $table->float('latest_cracking_ratio')->nullable();
            $table->float('latest_rutting_max')->nullable();
            $table->float('latest_rutting_ave')->nullable();
            $table->float('latest_IRI')->nullable();
            $table->float('latest_MCI')->nullable();
            $table->float('latest2_cracking')->nullable();
            $table->float('latest2_patching')->nullable();
            $table->float('latest2_pothole')->nullable();
            $table->float('latest2_cracking_ratio')->nullable();
            $table->float('latest2_rutting_max')->nullable();
            $table->float('latest2_rutting_ave')->nullable();
            $table->float('latest2_IRI')->nullable();
            $table->float('latest2_MCI')->nullable();
            $table->smallInteger('section_length')->nullable();
            $table->string('completion_year', 4)->nullable();
            $table->string('completion_month', 2)->nullable();
            $table->string('r_category_code', 10)->nullable();
            $table->string('r_classification_code', 10)->nullable();
            $table->decimal('total_traffic_volume', 20, 2)->nullable();
            $table->decimal('heavy_traffic', 20, 2)->nullable();
            $table->string('year_of_dataset', 4);  
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
        Schema::dropIfExists('tblPMS_dataset_info');
    }
}
