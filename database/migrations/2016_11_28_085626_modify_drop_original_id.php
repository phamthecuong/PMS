<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDropOriginalId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
		// Schema::table('tblSegment', function ($table) {
		    // $table->dropColumn('original_id');
		// });
// 		
		Schema::table('tblOrganization', function ($table) {
			// $table->dropColumn('effect_at');
			// $table->dropColumn('nullity_at');
			$table->dateTime('effect_at')->nullable();
			$table->dateTime('nullity_at')->nullable();
		    $table->dropColumn('original_id');
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
