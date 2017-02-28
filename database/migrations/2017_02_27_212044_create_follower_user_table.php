<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowerUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follower_user', function (Blueprint $table) {
            $table->integer('follower_id')->unsigned();
            $table->integer('user_id')->unsigned();
            
            $table->primary(['follower_id', 'user_id']);
            
            $table->foreign('follower_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follower_user');
    }
}
