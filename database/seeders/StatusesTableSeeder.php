<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    public function run()
    {
        Status::factory()->count(100)->create();
    }
}
