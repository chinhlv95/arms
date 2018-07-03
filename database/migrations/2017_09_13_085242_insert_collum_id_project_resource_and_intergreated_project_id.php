<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertCollumIdProjectResourceAndIntergreatedProjectId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('id_project_resource')->after('plan_end_time')->default(0);// 0: Mtool
            $table->integer('intergreated_project_id')->after('id_project_resource')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('id_project_resource');
            $table->dropColumn('intergreated_project_id');
        });
    }
}
