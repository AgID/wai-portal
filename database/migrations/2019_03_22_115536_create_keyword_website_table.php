<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Keyword - website pivot table creation - migration script.
 */
class CreateKeywordWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('keyword_website', function (Blueprint $table) {
            $table->integer('website_id')->unsigned()->index();
            $table->integer('keyword_id')->unsigned()->index();

            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->foreign('keyword_id')->references('id')->on('keywords')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_website');
    }
}
