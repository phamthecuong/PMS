<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTypeTblSegment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	// Schema::table('tblSegment', function ($table){
			// $table->decimal('m_from', 6, 2)->change();	
			// $table->decimal('m_to',6,2)->change();
		// });
// 		
        Schema::table('tblSegment_history', function ($table) {
			$sql = 'ALTER TABLE tblSegment_history
                    DROP COLUMN status 
                    ';
        	DB::connection()->getPdo()->exec($sql);
            $table->decimal('m_from',6,2)->change();	
			$table->decimal('m_to',6,2)->change();
        });
		
		// Schema::table('tblSegment_history', function ($table) {
			// $table->enum('status', ['insert', 'delete','update']);
        // });
		
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
