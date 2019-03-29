<?php

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
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Nome',
            'familyName' => 'Cognome',
            'fiscalNumber' => 'FSCLNB17A01H501X',
            'email' => 'nome.cognome@example.com',
            'uuid' => Uuid::uuid4()->toString(),
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'password_changed_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        Bouncer::scope()->to(0);
        User::findByFiscalNumber('FSCLNB17A01H501X')->assign('super-admin');
    }
}
