<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Captain;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Trip::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bus_id' => function () {
                return Bus::inRandomOrder()->first()->id;
            },
            'captain_id' => function () {
                return Captain::inRandomOrder()->first()->id;
            },
            'leaves_at' => $this->faker->dateTimeInInterval('-10 days', '+10 days'),
            'duration_in_minutes' => $this->faker->randomElement([30, 45, 60, 75, 90]),
        ];
    }
}
