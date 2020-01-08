<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Public Administrations table creation - migration script.
 */
class CreatePublicAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('public_administrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipa_code')->unique();
            $table->string('name');
            $table->string('pec')->nullable();
            $table->string('rtd_name')->nullable();
            $table->string('rtd_mail')->nullable();
            $table->string('rtd_pec')->nullable();
            $table->string('token_auth')->nullable();
            $table->integer('rollup_id')->unsigned()->nullable();
            $table->string('city');
            $table->string('county');
            $table->string('region');
            $table->string('type');
            $table->tinyInteger('status')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('public_administrations');
    }
}
