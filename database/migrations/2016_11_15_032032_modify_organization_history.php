<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOrganizationHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblOrganization_history', function (Blueprint $table) {
            $table->dropColumn(array('parent_id', 'created_by', 'updated_by'));
        });

        Schema::table('tblOrganization_history', function (Blueprint $table) {
            $table->integer('organization_id');
            $table->integer('original_id')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->timestamp('created_by')->nullable();
            $table->timestamp('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblOrganization_history', function (Blueprint $table) {
            //
        });
    }
}
