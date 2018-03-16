<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('public_administration_id')->unsigned()->index();
            $table->foreign('public_administration_id')->references('id')->on('public_administrations')->onDelete('cascade');
            $table->string('url');
            $table->string('type'); //TODO: define enum
            $table->string('analytics_id')->nullable();
            $table->string('slug')->unique();
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
        Schema::dropIfExists('websites');
    }
}
