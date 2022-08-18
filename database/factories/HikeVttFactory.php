<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class HikeVttFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(rand(3, 8), true),
            'description' => $this->faker->sentences(15, true),
            'public_price' => '6',
            'private_price' => '4',
            'address_id' => rand(1, 2),
            'club_id' => rand(1, 2),
            'date' => $this->faker->dateTimeBetween('+30 week', '+150 week'),
            'flyer' => $this->faker->sentence(1, true) . 'png',
            'created_at' => Carbon::now()
        ];
    }
}
