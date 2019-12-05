<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Silber\Bouncer\BouncerFacade as Bouncer;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $fiscalNumber = env('APP_SUPER_ADMIN_FISCAL_NUMBER');
        if (null === User::findByFiscalNumber($fiscalNumber)) {
            DB::table('users')->insert([
                'name' => env('APP_SUPER_ADMIN_NAME'),
                'family_name' => env('APP_SUPER_ADMIN_FAMILY_NAME'),
                'fiscal_number' => $fiscalNumber,
                'email' => env('APP_SUPER_ADMIN_EMAIL'),
                'uuid' => Uuid::uuid4()->toString(),
                'password' => Hash::make(env('APP_SUPER_ADMIN_PASSWORD')),
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'password_changed_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            Bouncer::scope()->to(0);
            User::findByFiscalNumber($fiscalNumber)->assign(UserRole::SUPER_ADMIN);
        }
    }
}
