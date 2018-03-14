<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('permissions', function (Blueprint $table) {
        //     $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('cascade');
        // });
        // Schema::table('user_roles', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        // });
        // Schema::table('user_permissions', function (Blueprint $table) {
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        // });
        // Schema::table('role_permissions', function (Blueprint $table) {
        //     $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        //     $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
   //      Schema::table('permissions', function (Blueprint $table) {
   //          $table->dropForeign('permission_group_id_foreign');
   //      });
   //      Schema::table('user_roles', function (Blueprint $table) {
   //      	$table->dropForeign('user_id_foreign');
			// $table->dropForeign('role_id_foreign');
   //      });
   //      Schema::table('user_permissions', function (Blueprint $table) {
   //      	$table->dropForeign('user_id_foreign');
			// $table->dropForeign('permission_id_foreign');
   //      });
   //      Schema::table('role_permissions', function (Blueprint $table) {
   //      	$table->dropForeign('role_id_foreign');
			// $table->dropForeign('permission_id_foreign');
   //      });
    }
}
