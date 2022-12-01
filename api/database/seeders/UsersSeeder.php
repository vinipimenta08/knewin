<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRoot = Users::create([
            'name' => 'admin',
            'email' => 'admin@phplaravel.com',
            'password' => bcrypt(env('DB_PASSWORD')),
            'active' => 1
        ]);




    }
}
