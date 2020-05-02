<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdatePublicAdministrationUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->map(function ($user) {
            if ($user->publicAdministrations->isNotEmpty()) {
                $publicAdministrationsIdWithEmailAndStatus = $user->publicAdministrations->pluck('name', 'id')->map(function () use ($user) {
                    return ['user_email' => $user->email, 'user_status' => $user->status->value];
                })->toArray();
                $user->publicAdministrations()->sync($publicAdministrationsIdWithEmailAndStatus);
            }
        });
    }
}
