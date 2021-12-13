<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::reguard();

        // Admin
        $user = User::forceCreate([
            'name' => env('SEEDER_USER_FIRST_NAME', 'Webexert'),
            'sur_name' => env('SEEDER_USER_LAST_NAME', 'Pak'),
            'password' => bcrypt((env('SEEDER_USER_PASSWORD', '123123'))),
            'email' => env('SEEDER_USER_EMAIL', 'admin@webexert.com'),

        ]);
        $user->setEmailVerifiedDate();
        $user->setAdmin(true);

        // User
        $user = User::forceCreate([
            'name' => 'Mudassar',
            'sur_name' => 'Irshad',
            'password' => bcrypt('123123'),
            'email' => 'user@webexert.com',
        ]);
        $user->setEmailVerifiedDate();

    }
}
