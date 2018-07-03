<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingLengthClumnPhonenumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','phone'))
            {
                $table->dropColumn('phone');   
            }
            else{
                $table->string('phone', 50)->nullable()->after('calling_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users','phone'))
        {
            Schema::table('users', function (Blueprint $table) {
                 $table->dropColumn('phone');
            });
        }
    }
}
