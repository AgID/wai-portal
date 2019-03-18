<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
            'password' => Hash::make('password'),
            'status' => 'active',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        Bouncer::scope()->to(0);
        User::findByFiscalNumber('FSCLNB17A01H501X')->assign('super-admin');
    }
}
