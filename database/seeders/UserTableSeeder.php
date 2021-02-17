<?php

namespace Database\Seeders;

use App\User;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'role_id' => 1,
            'password' => app('hash')->make('secret')
        ]);
    }
}
