<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Line;
use App\Models\Station;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            Line::factory()->has(
                Trip::factory()->count(100)
            )
            ->has(
                Station::factory()
                    ->count(10)
                    ->state(new Sequence(
                        ...City::inRandomOrder()->take(10)->pluck('slug')
                            ->map(function ($item) {
                                return ['city_slug' => $item];
                            })->all()
                    ))
                    ->state(new Sequence(
                        ...collect(range(1, 10))
                            ->map(function ($item) {
                                return ['order' => $item];
                            })->all()
                    ))
            )
            ->create();
        }
    }
}
