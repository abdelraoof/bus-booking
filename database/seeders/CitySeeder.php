<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::factory()->create(['name' => 'Cairo']);
        City::factory()->create(['name' => 'Giza']);
        City::factory()->create(['name' => 'AlFayyum']);
        City::factory()->create(['name' => 'AlMinya']);
        City::factory()->create(['name' => 'Asyut']);
        City::factory()->count(20)->create();
    }
}
