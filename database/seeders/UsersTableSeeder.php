<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(100)->create();

        $user = User::find(1);
        $user->name = 'chenyong';
        $user->email = 'chenyong@example.com';
        $user->is_admin = TRUE;
        $user->save();
    }
}
