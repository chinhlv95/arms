<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMtoolEntryIdToWorktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('worktimes', function (Blueprint $table) {
            $table->integer('mtool_entry_id')->after('id_resource')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('worktimes', function (Blueprint $table) {
            $table->dropColumn('mtool_entry_id');
        });
    }
}
