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
            $table->string('spidCode')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('familyName')->nullable();
            $table->string('fiscalNumber');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('public_administration_id')->unsigned()->nullable();
            $table->foreign('public_administration_id')->references('id')->on('public_administrations');
            $table->enum('status', ['invited', 'inactive', 'pending', 'active', 'suspended']);
            $table->string('analytics_password')->nullable();
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
