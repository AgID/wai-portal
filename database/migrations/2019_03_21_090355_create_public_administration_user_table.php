<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * User - Public Administration pivot table creation - migration script.
 */
class CreatePublicAdministrationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('public_administration_user', function (Blueprint $table) {
            $table->integer('public_administration_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();

            $table->foreign('public_administration_id')->references('id')->on('public_administrations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('public_administration_user');
    }
}
