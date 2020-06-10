<?php

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AddEmailAndStatusToPublicAdministrationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_administration_user', function (Blueprint $table) {
            $table->string('user_email')->nullable();
            $table->tinyInteger('user_status')->unsigned()->default(UserStatus::INACTIVE);
        });

        Artisan::call('db:seed', ['--class' => 'UpdatePublicAdministrationUser']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_administration_user', function (Blueprint $table) {
            $table->dropColumn(['user_email', 'user_status']);
        });
    }
}
