<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname',255);
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email');
            $table->string('avatar',255)->nullable();
            $table->string('calling_code',255);
            $table->integer('phone',false,true)->nullable();
            $table->string('skype',255)->nullable();
            $table->text('permission');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
