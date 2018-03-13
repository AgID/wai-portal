<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            'spidCode' => 'TEST0123456789',
            'name' => 'Nome',
            'familyName' => 'Cognome',
            'fiscalNumber' => 'FSCLNB17A01H501X',
            'email' => 'nome.cognome@example.com',
            'status' => 'pending',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
