<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('spidCode')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('familyName')->nullable();
            $table->string('fiscalNumber')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('public_administration_id')->unsigned()->nullable();
            $table->foreign('public_administration_id')->references('id')->on('public_administrations');
            $table->enum('status', ['invited', 'inactive', 'pending', 'active', 'suspended']);
            $table->string('partial_analytics_password')->nullable();
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
