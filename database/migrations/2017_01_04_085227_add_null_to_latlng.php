<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullToLatlng extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tblMH_history', function (Blueprint $table) {
            $sql = 'ALTER TABLE tblMH_history
                    DROP COLUMN from_lat,
                    DROP COLUMN from_lng,
                    DROP COLUMN to_lat,
                    DROP COLUMN to_lng
                    ';
            DB::connection()->getPdo()->exec($sql);
            $table->decimal('from_lat', 10, 8)->nullable();
            $table->decimal('from_lng', 11, 8)->nullable();
            $table->decimal('to_lat', 10, 8)->nullable();
            $table->decimal('to_lng', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tblMH_history', function (Blueprint $table) {
            //
        });
    }
}
