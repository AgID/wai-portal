<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNullabeFieldsPublicAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_administrations', function (Blueprint $table) {
            $table->string('city')->nullable()->change();
            $table->string('county')->nullable()->change();
            $table->string('region')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_administrations', function (Blueprint $table) {
            $table->string('city')->nullable(false)->change();
            $table->string('county')->nullable(false)->change();
            $table->string('region')->nullable(false)->change();
        });
    }
}
