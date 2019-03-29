<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website keywords table creation - migration script.
 */
class CreateKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vocabulary');
            $table->string('id_vocabulary');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['vocabulary', 'id_vocabulary']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
}
