<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCollumIdResourceTableProjectUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_user', function (Blueprint $table) {
            $table->integer('id_resource')->default(2)->comment('0: LDap; 1: ARMS System-enable edit, 2: Mtool-disable edit')->change();// 2: Mtool(not edit), 1: ARMS System
            $table->float('work_time', 8, 2)->after('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_user', function (Blueprint $table) {
        });
    }
}
