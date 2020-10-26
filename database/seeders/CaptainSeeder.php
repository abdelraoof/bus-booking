<?php

namespace Database\Seeders;

use App\Models\Captain;
use Illuminate\Database\Seeder;

class CaptainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Captain::factory()->count(10)->create();
    }
}
