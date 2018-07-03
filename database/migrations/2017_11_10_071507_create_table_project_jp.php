<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectJp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_jp', function(Blueprint $table){
            $table->increments('id');
            $table->char('code', 9);
            $table->string('name', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->char('client_code', 4)->nullable();
            $table->string('client_name', 100)->nullable();
            $table->date('order_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('acceptance_date')->nullable();
            $table->date('plan_completion_date')->nullable();
            $table->string('chief_staff')->nullable();
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
        Schema::dropIfExists('project_jp');
    }
}
