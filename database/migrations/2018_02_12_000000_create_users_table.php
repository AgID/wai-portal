<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users table creation - migration script.
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('spid_code')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('uuid')->index();
            $table->string('family_name')->nullable();
            $table->string('fiscal_number')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('public_administration_id')->unsigned()->nullable();
            $table->foreign('public_administration_id')->references('id')->on('public_administrations');
            $table->tinyInteger('status')->unsigned();
            $table->string('partial_analytics_password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('last_access_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
