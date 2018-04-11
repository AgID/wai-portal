<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_administrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipa_code')->unique();
            $table->string('name');
            $table->string('pec_address')->nullable();
            $table->string('city');
            $table->string('county');
            $table->string('region');
            $table->string('type');
            $table->enum('status', ['pending', 'active', 'suspended']);
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
        Schema::dropIfExists('public_administrations');
    }
}
